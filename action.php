<?php
/**
 * DokuWiki Plugin bookmark2wiki (Action Component)
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  dodotori <dodotori@localhost>
 * forked from post2wiki.php by riny [at] bk [dot] ru
 * To bookmark webpage using bookmarklet
 * The app will add the url, title and hightlighed text you want to the end of the content of the targeted namespace. It does not directly read/white the dokuwiki page folder.
 * Version 1.0 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     dodotori https://github.com/edwardcodelab   
         **/
 
	// TYPICAL USAGE :     
        //                 Create bookmarklet as shown in the bookmarklet part below: 
        //                 Change the window.open statement to reflect the location of the bookmark2wiki.php script.
        //                 Drag bookmarklet to your toolbar.
        //  BOOKMARKLET : 
	//  javascript:Q=document.selection?document.selection.createRange().text:document.getSelection(); void(window.open('https://myserver/doku.php?do=bookmark2wiki&te='+escape(Q)+'&ur='+ escape(location.href)+'&ti='+escape(document.title),'dokuwikiadd','scrollbars=yes,resizable=yes,toolbars=yes,width=200,height=100,left=200,top=200,status=yes'));
 


class action_plugin_bookmark2wiki extends \dokuwiki\Extension\ActionPlugin
{

    /** @inheritDoc */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE',  $this, 'allowMyAction');
        $controller->register_hook('TPL_ACT_UNKNOWN', 'BEFORE',  $this, 'performMyAction');
    
    }

    /**
     * FIXME Event handler for
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  optional parameter passed when event was registered
     * @return void
     */


     public function allowMyAction(Doku_Event $event, $param) {
        if($event->data != 'bookmark2wiki') return; 
        $event->preventDefault();
    }
 
    public function performMyAction(Doku_Event $event, $param) {
       if($event->data != 'bookmark2wiki') return; 
       $event->preventDefault();
               
        echo'<button id="closedb" onclick ="window.close()">Back to Dokuwiki</button>';
      	// SETUP SECTION
	$namespace="new_bookmarks";  // default namespace for bookmark 
	// POST TO WIKI
	$timestamp = date("Y:m:d:H:i:s"); //timestamp
	$wikitext=$_GET['te'];  // things to log : Selected text
	$url=$_GET['ur'];       // things to log : URL
    $title=$_GET['ti'];     // things to log : title
	$string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $title); // convert the unicode
    $title = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
    $string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $wikitext);
    $wikitext = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
	$bookmarktext="$url";
	$targeturl = DOKU_BASE."doku.php?id=".$namespace."&do=edit";    
	      echo '<script>';
          echo 'function loadFunc(){';
          echo 'if(document.getElementById("top").contentWindow.document.getElementsByTagName("textarea")[0].innerHTML.length==0){document.getElementById("top").contentWindow.document.getElementsByTagName("textarea")[0].innerHTML = " ====== New Bookmarks ======"};';
          echo 'temp = document.getElementById("top").contentWindow.document.getElementsByTagName("textarea")[0].innerHTML.split(/\n/);';
          echo 'temp[1] = "    * [['.$url.'|'.$title.']] \\\\\\\\ '.$wikitext.' -- '.$timestamp.'\r\n" + temp[1];';
          echo 'document.getElementById("top").contentWindow.document.getElementsByTagName("textarea")[0].innerHTML = temp.join("\n");';
          echo '};';
          echo '</script>';
          echo '<iframe src="'.$targeturl.'" id="top" width="100%" height= "1000 px" onload="loadFunc()"></iframe>';        
    }

}
