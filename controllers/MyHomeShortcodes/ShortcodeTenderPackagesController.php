<?php

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('ShortcodeTenderPackagesController'))
  return;

/**
 * The ShortcodeTenderPackagesController class
 *
 * Controller for the TenderPackage shortcode
 *
 * @since 1.6
 */
class ShortcodeTenderPackagesController extends ShortcodeTenderBaseController {

  /**
   * {@inheritDoc}
   */
  public function doPostXhr(array $params=[]){
    //myHome()->log->info('ShortcodeTenderPackagesController.doPostXhr: ' . serialize($params));

    if($params['tenderId']<=0)
      myHome()->abort(500,'No tender ID');
    //if($params['myHomePackageCategoryId']<=0)
    //  myHome()->abort(500,'No category ID');
    if($params['packageId']<=0)
      myHome()->abort(500,'No package ID');

    try{
      //if($params['packageSelected'] == 'true')
        $response = myHome()->api->put(sprintf('tenders/%u/packages', $params['tenderId']), [[
          'tenderPackageId' => (int) $params['packageId'],
          'selected' => $params['packageSelected'] == 'true'
        ]], myHome()->session->getAuthentication());
        //$response = myHome()->api->post(sprintf('tenders/%u/packages/%u', $params['tenderId'], $params['packageId']), [], myHome()->session->getAuthentication());
      //else
      //  $response = myHome()->api->delete(sprintf('tenders/%u/packages/%u', $params['tenderId'], $params['packageId']), [], myHome()->session->getAuthentication());

      // Handling error responses has been moved to myHome()->api->request
      //if($response === null)
      //  myHome()->abort(400,'Package update failed'); // Bad request

      echo json_encode($response); //['ok'=>1]);
    }
    catch(Exception $e){
      $className=(new ReflectionClass($e))->getShortName();
      myHome()->abort(500,sprintf('Error while trying to update package (%s): %s',$className,$e->getMessage()));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function doShortcode(array $atts=[]){
	  //$atts = shortcode_atts(['showitemprices'=>'true'],$atts);
    $atts['content'] = isset($atts['content']) ? $atts['content'] : '';
    $atts['showitemprices'] = isset($atts['showitemprices']) ? $atts['showitemprices']==='true' : true;

    //$tenderId = $this->getParam('myHomeTenderId');
   // $categoryId = $this->getParam('myHomeTenderPackageCategoryId');

    //if($tenderId===null)
    //  throw new MyHomeException('Tender ID not provided');
    //if($categoryId===null) 
      //throw new MyHomeException('Category ID not provided');

    $tender = $this->tender($this->getParam('myHomeTenderId'));
    $categories = $this->categories($this->getParam('myHomeTenderId'));
    $category = count($categories) ? $this->category($this->getParam('myHomeTenderId'), $this->getParam('myHomeTenderPackageCategoryId') ? $this->getParam('myHomeTenderPackageCategoryId') : $categories[0]->id): null; // $categoryId);

    if($tender===null)
      throw new MyHomeException(sprintf('Tender %u not available', $tenderId));
    //else if(!$packages)
		//  throw new MyHomeException(sprintf('Tender %u has no available packages', $tenderId));

    //$tenderPages=myHome()->options->getTenderPages();
    //$overviewUrlBase=get_permalink($tenderPages['overview']);
    //$params['myHomeTenderId']=$tenderId;
    //$overviewUrl=add_query_arg($params,$overviewUrlBase);

    $this->loadView('shortcodeTenderPackages','MyHomeShortcodes',compact('categories','category','atts'));
  }

  /**
   * Returns the tender package list
   *
   * @uses MyHomeApi::get()
   * @return null|string[][] the tenders selections (null if not available) - each item is composed of:
   * <ul>
   * <li>id</li>
   * <li>name</li>
   * <li>description</li>
   * <li>outstanding</li>
   * <li>editUrl</li>
   * </ul>
   */
  private function categories($tenderId){
    $categories = myHome()->api->get(sprintf('tenders/%u/packages/categories',$tenderId), myHome()->session->getAuthentication());
    //myHome()->log->info(json_encode($categories));
    //var_dump($categories);

    if($categories===null) {
      return null;
    }

    /*$attributes = [
      'id',
      'name',
      'description',
      'packages'
    ];*/

    $tenderPages = myHome()->options->getTenderPages();
    if(!isset($tenderPages['packages']))
      throw new MyHomeException('Tender Packages page not set');
    
    // Categories
    $categories = array_map(function($category) use ($tenderId, $tenderPages) {
      $category->editUrl = add_query_arg([
        'myHomeTenderId' => $tenderId,
        'myHomeTenderPackageCategoryId' => $category->id
      ], get_permalink($tenderPages['packages']));
      //var_dump($category->editUrl);

      return $category;
    }, (array) $categories);

    $categories = array_filter($categories);
    return $categories;
  }

  /**
   * @param int $id
   * @param int $categoryId
   * @return mixed[]|null
   */
  private function category($tenderId, $categoryId){
    //if(!isset($this->categories[0])) throw new MyHomeException('Category not found');
    //if(!isset($categoryId)) $categoryId = $this->categories[0]->id; // If none provided, get the first available
    //var_dump($this->tender);
    //$this->category = myHome()->helpers->findWhere($this->categories, 'id', $categoryId);
    $category = myHome()->api->get(sprintf('tenders/%u/packages?categoryId=%u', $tenderId, $categoryId), myHome()->session->getAuthentication());
    //var_dump($category);

    if($category === null || count((array) $category) == 0) 
      return null;
      //throw new MyHomeException('Category not available');

    //if(!isset($category->packages)) $category->packages = [];
    
    // Sanitise package data
    $category->packages = array_map(function($package) {
      if($package->sellPrice)
        $package->sellPrice = myHome()->helpers->formatDollars($package->sellPrice);
      
      return $package;
    }, $category->packages);

    return $category;
  }
}
