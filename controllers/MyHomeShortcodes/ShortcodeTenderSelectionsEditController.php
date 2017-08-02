<?php

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('ShortcodeTenderSelectionsEditController'))
  return;

/**
 * The ShortcodeTenderSelectionsEditController class
 *
 * Controller for the TenderSelectionsEdit shortcode
 *
 * @since 1.5
 */
class ShortcodeTenderSelectionsEditController extends ShortcodeTenderBaseController{
  /**
   * {@inheritDoc}
   */
  public function doPostXhr(array $params=[]){
    if($params['tenderId'] <= 0)
      myHome()->abort(500,'No tender ID');
    if($params['categoryId'] <= 0)
      myHome()->abort(500,'No category ID');

    //myHome()->log->info(json_encode($params['selections']));

    try{
      $response = myHome()->api->put(sprintf('tenders/%u/selections', $params['tenderId']), $params, myHome()->session->getAuthentication());

      // Handling error responses has been moved to myHome()->api->request
      //if($category===null)
      //  myHome()->abort(400,'Selection update failed'); // Bad request

      echo json_encode($response); //['ok'=>1]);
    }
    catch(Exception $e){
      $className=(new ReflectionClass($e))->getShortName();
      myHome()->abort(500,sprintf('Error while trying to update selections (%s): %s',$className,$e->getMessage()));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function doShortcode(array $atts=[]){
	  //$atts=shortcode_atts(['content'=>'','showitemquantities'=>'false', 'showitemprices'=>'false', 'showrunningquantities'=>'false', 'showrunningprices'=>'false'],$atts);
    $atts['content'] = isset($atts['content']) ? $atts['content'] : '';
    $atts['showitemquantities'] = isset($atts['showitemquantities']) ? $atts['showitemquantities'] === 'true' : true;
    $atts['showitemprices'] = isset($atts['showitemprices']) ? $atts['showitemprices'] === 'true' : true;
    $atts['showrunningquantities'] = isset($atts['showrunningquantities']) ?  $atts['showrunningquantities'] === 'true' : true;
    $atts['showrunningprices'] = isset($atts['showrunningprices']) ? $atts['showrunningprices'] === 'true' : true;
    
    $tender = $this->tender($this->getParam('myHomeTenderId')); //var_dump($tender);

    $categories = $this->categories($tender->tenderid); //var_dump($categories);

    $category = $this->category($tender->tenderid, $this->getParam('myHomeTenderSelectionCategoryId') ? $this->getParam('myHomeTenderSelectionCategoryId') : $categories[0]->optionCategoryId);
    //var_dump($category);

    $this->loadView('shortcodeTenderSelectionsEdit','MyHomeShortcodes', compact('categories','category','atts'));
  }


  /**
   * Returns the tender selection categories
   *
   */
  private function categories($id){
    $categories = myHome()->api->get(sprintf('tenders/%u/selections',$id), myHome()->session->getAuthentication());
    //var_dump($categories); 

    if($categories===null || !is_array($categories))
      return null;
    //var_dump($tender->substitutionCategories);

    $tenderPages = myHome()->options->getTenderPages();
    if(!empty($tenderPages['selectionsEdit'])){
      $editUrlBase = get_permalink($tenderPages['selectionsEdit']);

      $params['myHomeTenderId'] = $id;
      $editUrlBase = add_query_arg($params,$editUrlBase);
    }
    else
      $editUrlBase = null;

    $attributes=
      [//'optionCategoryId',
        'categoryName',
       // 'description',
        'outStandingCount'];

    $categories = array_map(function($category) use ($editUrlBase, $attributes, $id){
      if(!is_object($category))
        return null;

      foreach($attributes as $attribute)
        if(!isset($category->{$attribute}))
          return null;

      if($editUrlBase){
        $params['myHomeTenderSelectionCategoryId'] = $category->optionCategoryId;
        $category->editUrl = add_query_arg($params,$editUrlBase);
      }
      else
        $category->editUrl = null;

      /*if(!empty($category->categoryImage))
        $photoUrl=$category->categoryImage;
      else
        $photoUrl=null;*/

    //  $categoryDetails=$this->categoryDetails($id,$category->optionCategoryId);
      //var_dump($categoryDetails);

      /**
       * @var int     $totalSelections
       * @var mixed[] $currentSelections
       */
   //   if($categoryDetails!==null)
   //     extract($categoryDetails);
   //   else{
   //     $totalSelections=0;
   //     $currentSelections=[];
   //   }
      //var_dump($totalSelections);
      
      return $category;
     /* return
        (object) [//'id'=>$category->optionCategoryId,
          'name'=>$category->categoryName,
          //'description'=>$category->description,
          'outstanding'=>$category->outStandingCount,
          'editUrl'=>$editUrl,
          //'photoUrl'=>$photoUrl,
          //'totalSelections'=>$totalSelections,
          //'currentSelections'=>$currentSelections
        ];*/
    }, $categories);

    $categories = array_filter($categories);

    return $categories;
  }

  /**
   * Returns the tender selection category
   *
   * @uses MyHomeApi::get()
   * @param int $tenderId
   * @param int $categoryId
   * @return null|mixed[] the tenders selection details (null if not available):
   */
  private function category($tenderId, $categoryId){
    $category = myHome()->api->get(sprintf('tenders/%u/selections?categoryId=%u', $tenderId, $categoryId), myHome()->session->getAuthentication());
    //var_dump(count($category->selections));
    //myHome()->log->info('$category ' . serialize($category));'

    if($category===null)
      return null;

    if(isset($category->selections[0]->substitutionOptions)) {
      foreach($category->selections[0]->substitutionOptions as $option) {
        if(!$option->thumbnail && count($option->documents)) $option->thumbnail = $option->documents[0]->docId;
      }
    }

    return $category;
  }
}
