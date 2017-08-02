<?php

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('ShortcodeTenderSelectionsController'))
  return;

/**
 * The ShortcodeTenderSelectionsController class
 *
 * Controller for the TenderSelection shortcode
 *
 * @since 1.5
 */
class ShortcodeTenderSelectionsController extends ShortcodeTenderBaseController{
  /**
   * {@inheritDoc}
   */
  public function doShortcode(array $atts=[]){
	  //$atts = shortcode_atts(['content'=>'', 'showitemquantities'=>'true', 'showitemprices'=>'true', 'showrunningquantities'=>'false', 'showrunningprices'=>'false'],$atts);
    $atts['content'] = isset($atts['content']) ? $atts['content'] : '';
    $atts['showitemquantities'] = isset($atts['showitemquantities']) ? $atts['showitemquantities'] === 'true' : true;
    $atts['showitemprices'] = isset($atts['showitemprices']) ? $atts['showitemprices'] === 'true' : true;
    $atts['showrunningquantities'] = isset($atts['showrunningquantities']) ?  $atts['showrunningquantities'] === 'true' : true;
    $atts['showrunningprices'] = isset($atts['showrunningprices']) ? $atts['showrunningprices'] === 'true' : true;
    $atts['showsummaries'] = isset($atts['showsummaries']) ? $atts['showsummaries'] === 'true' : true;

    // Set & return global tender object
    $tender = $this->tender($this->getParam('myHomeTenderId')); //var_dump($tender);

    $categories = $this->categories($tender->tenderid, $atts['showsummaries']);

    $this->loadView('shortcodeTenderSelections','MyHomeShortcodes',compact('categories','atts'));
  }

  /**
   * Returns the tender selection list
   *
   * @uses MyHomeApi::get()
   * @param int $id
   * @return null|string[][] the tenders selections (null if not available) - each item is composed of:
   * <ul>
   * <li>id</li>
   * <li>name</li>
   * <li>description</li>
   * <li>outstanding</li>
   * <li>editUrl</li>
   * </ul>
   */
  private function categories($id, $showSummaries){
    $categories = myHome()->api->get(sprintf('tenders/%u/selections',$id), myHome()->session->getAuthentication()); 
    //var_dump($categories); 
    if($categories===null || !is_array($categories))
      return null;

    $tenderPages = myHome()->options->getTenderPages();
    $editUrlBase = !empty($tenderPages['selectionsEdit']) ? get_permalink($tenderPages['selectionsEdit']) : '';

    $categories = array_map(function($category) use ($id, $editUrlBase, $showSummaries) {
      $category->currentSelections = [];
      $category->totalSelections = 37;

      $category->editUrl = add_query_arg([
        'myHomeTenderId' => $id,
        'myHomeTenderSelectionCategoryId' => $category->optionCategoryId
      ], $editUrlBase);
      
      $category->currentSelections = [];
      if($showSummaries) {
        foreach($this->categoryDetails($id, $category->optionCategoryId)->selections as $selection) {
          if( !isset($selection->substitutionOptions) || !is_array($selection->substitutionOptions)) continue;
           
          foreach($selection->substitutionOptions as $option) {
            if($option->selectCount > 0) $category->currentSelections[] = $option;
          }
        }
      }

      return $category;
    }, $categories);

    return $categories;
  }

  /**
   * @param int $id
   * @param int $categoryId
   * @return mixed[]|null
   */
  private function categoryDetails($id,$categoryId){
    $category = myHome()->api->get(sprintf('tenders/%u/selections?categoryId=%u',$id,$categoryId), myHome()->session->getAuthentication());
    
    if($category===null || !isset($category->selections) || !is_array($category->selections))  return null;

    //var_dump($category->selections);
    return $category;
  }
}
