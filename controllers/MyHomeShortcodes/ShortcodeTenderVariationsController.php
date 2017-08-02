<?php

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('ShortcodeTenderVariationsController'))
  return;

/**
 * The ShortcodeTenderVariationsController class
 *
 * Controller for the TenderVariations shortcode
 *
 * @since 1.6
 */
class ShortcodeTenderVariationsController extends ShortcodeTenderBaseController{

  /**
   * {@inheritDoc}
   */
  public function doPostXhr(array $params=[]){
    //myHome()->log->info('ShortcodeTenderVariationController doPostXhr: ' . serialize($params));
    if(!isset($params['tenderId']))
      myHome()->abort(400,'tenderId not provided'); // Bad-Request
    else if(!isset($params['variationId']))
      myHome()->abort(400,'variationId not provided'); // Bad-Request
    else if(!isset($params['data']))
      myHome()->abort(400,'Signature not provided'); // Bad-Request
    //else if(strlen($params['data']) < 200)
    //  myHome()->abort(400,'Signature too short: ' . strlen($params['data']) . ' : ' . $params['data']); // Bad-Request

    $response = myHome()->api->post('tenders/' . $params['tenderId'] . '/variations/' . $params['variationId'] . '/clientApprove', $params, myHome()->session->getAuthentication(), true);

    //myHome()->log->info('ShortcodeTenderVariationController doPostXhr response: ' . $response);
    echo json_encode($response);
  }

  /**
   * {@inheritDoc}
   */
  public function doShortcode(array $atts=[]){
	  //$atts=shortcode_atts(['showitemquantities'=>'true'],$atts);
    $atts['content'] = isset($atts['content']) ? $atts['content'] : '';
    //$attShowItemQuantities=$atts['showitemquantities']==='true';

    // Set & return global tender object
    $tender = $this->tender($this->getParam('myHomeTenderId'));

    $variations = $this->variations();

    /* Disabled until API exists
    if($packages===null)
      throw new MyHomeException(sprintf('Tender %u not available', $tenderId));
    else if(!$packages)
		  throw new MyHomeException(sprintf('Tender %u has no available packages', $tenderId));*/
    //$selectedVariation=$this->selectedOption($packageCategories);

    $declaration = myHome()->options->getTenderVariationDeclaration();

    $this->loadView('shortcodeTenderVariations','MyHomeShortcodes',compact('variations', 'declaration', 'atts'));
  }

  /**
   * Returns the tender variation list
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
  private function variations(){
    global $tender;

    $variations = myHome()->api->get(sprintf('tenders/%u/variations', $tender->tenderid), myHome()->session->getAuthentication());

    return $variations;
  }

}
