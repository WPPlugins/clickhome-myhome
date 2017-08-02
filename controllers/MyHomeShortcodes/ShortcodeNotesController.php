<?php

/**
 * The ShortcodeNotesController class
 *
 * @package    MyHome
 * @subpackage ControllersShortcodes
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('ShortcodeNotesController'))
  return;

/**
 * The ShortcodeNotesController class
 *
 * Controller for the Notes shortcode
 */
class ShortcodeNotesController extends MyHomeShortcodesBaseController{
  /**
   * {@inheritDoc}
   */
  public function doGet(array $params=[]){
  }

  /**
   * {@inheritDoc}
   */
  public function doPost(array $params=[]){
  }

  /**
   * {@inheritDoc}
   */
  public function doPostXhr(array $params=[]){
    list($subject,$body)=$this->extractParams(['myHomeSubject',
      'myHomeBody'],$params);

    if($subject==='')
      myHome()->abort(400,'Note subject empty'); // Bad Request
    if($body==='')
      myHome()->abort(400,'Note body empty'); // Bad Request

    // Filter the subject and body
    $subject=$this->filterText($subject,250);
    $body=$this->filterText($body,10000);

    // Leave the replytoid and stepid as null by the moment
    $noteParams=['replytoid'=>null,
      'subject'=>$subject,
      'stepid'=>null,
      'body'=>$body];

    $authentication=myHome()->session->getAuthentication();
    $newNote=myHome()->api->post('notes',$noteParams,$authentication,true);

    if($newNote===null)
      myHome()->abort(403,'Note submission failed'); // Forbidden

    $note=$this->noteItem($newNote);
    $this->loadView(['shortcodeNotes','note'],'MyHomeShortcodes',compact('note'));
  }

  /**
   * {@inheritDoc}
   */
  public function doShortcode(array $atts=[]){
    $atts=shortcode_atts(['limit'=>'0',
      'hidenew'=>'false',
      'hidefields'=>''],$atts);

    $attLimit=(int)$atts['limit'];
    $attHideNew=$atts['hidenew'];
    $attHideFields=explode(',',$atts['hidefields']);
    $attHideFields=array_map('trim',$attHideFields);
    $attHideFields=array_filter($attHideFields,'strlen');

    if(!$this->verifyHideNew($attHideNew)){
      myHome()->handleError('Wrong Hide New attribute: '.$attHideNew);
      $attHideNew='false';
    }
    if(!$this->verifyHideFields($attHideFields)){
      myHome()->handleError('Wrong Hide Fields attribute: '.implode(',',$attHideFields));
      $attHideFields=[];
    }

    $notes=$this->notesList();
    if($notes===null)
      return;

    $this->loadView('shortcodeNotes','MyHomeShortcodes',compact('attLimit','attHideNew','attHideFields','notes'));
  }

  /**
   * Returns an array with details about a note
   *
   * <p>It is used by both doShortcode(), through notesList(), and doPostXhr() - the latter, to pass it as a local
   * varible to the notes.note subview</p>
   * <p>The keys in the array returned (author, subject, body and date) are the possible choices for the hidefields
   * attribute</p>
   *
   * @uses MyHomeBaseController::dateString()
   * @param stdClass      $note the note details, as returned by the API
   * @param DateTime|null $dt   the date and time of the note, if not null
   * @return string[] the note details:
   *                            <ul>
   *                            <li>author: note author (author field)</li>
   *                            <li>subject: note subject (subject field)</li>
   *                            <li>body: note body (body field)</li>
   *                            <li>date: note date (generated from the date field)</li>
   *                            </ul>
   */
  private function noteItem(stdClass $note,DateTime $dt=null){
    if($dt===null)
      $dt=new DateTime($note->notedate);

    $author='';
    $subject='';
    $body='';

    // To allow "0" as a valid author name, subject or body, we don't used empty() here
    if(isset($note->authorname)&&$note->authorname!==null)
      $author=$note->authorname;
    if(isset($note->subject)&&$note->subject!==null)
      $subject=$note->subject;
    if(isset($note->body)&&$note->body!==null)
      $body=$note->body;

    return ['author'=>$author,
      'subject'=>$subject,
      'body'=>$body,
      'date'=>$this->dateString($dt)];
  }

  /**
   * Returns the sorted notes list after querying the API with the jobsteps command
   *
   * @uses MyHomeApi::get()
   * @uses ShortcodeNotesController::noteItem() to retrieve each note item
   * @return mixed[]|null the notes list (null if not available), sorted by sequence number in ascending order - each
   *                      item is composed of:
   * <ul>
   * <li>Array key: note timestamp (generated from the notedate field)</li>
   * <li>author: note author (author field)</li>
   * <li>subject: note subject (subject field)</li>
   * <li>body: note body (body field)</li>
   * <li>date: note date (generated from the date field)</li>
   * </ul>
   */
  private function notesList(){
    $authentication=myHome()->session->getAuthentication();
    $notes=myHome()->api->get('notes',$authentication,true);

    if($notes===null)
      return null;

    $notesTimes=[];

    foreach($notes as $note){
      if(empty($note->notedate))
        continue;

      $dt=new DateTime($note->notedate);

      // The note timestamp is used as a key to sort the notes
      $time=$dt->getTimestamp();
      if(!isset($notesTimes[$time]))
        $notesTimes[$time]=[];

      $notesTimes[$time][]=$this->noteItem($note,$dt);
    }

    // Sort by timestamp (array key)
    krsort($notesTimes,SORT_NUMERIC);

    // Flatten the array - $notesList is made up of arrays of items, one per timestamp (although it is not very likely that two or more notes will share the exact same timestamp)
    $notesList=[];
    foreach($notesTimes as $notesTime)
      $notesList=array_merge($notesList,$notesTime);

    return $notesList;
  }

  /**
   * Verifies the value of the hidefields shortcode attribute provided
   *
   * @param string[] $hideFields the hidefields attribute value to check
   * @return bool whether the attribute is valid or not (it must not contain fields other than "author", "subject",
   *                             "body" and/or "date")
   */
  private function verifyHideFields(array $hideFields){
    $validFields=['author',
      'subject',
      'body',
      'date'];

    foreach($hideFields as $field)
      if(!in_array($field,$validFields))
        return false;

    return true;
  }

  /**
   * Verifies the value of the hidenew shortcode attribute provided
   *
   * @param string $hideNew the hidenew attribute value to check
   * @return bool whether the attribute is valid or not (it must be "false" or "true")
   */
  private function verifyHideNew($hideNew){
    return in_array($hideNew,['false','true']);
  }
}
