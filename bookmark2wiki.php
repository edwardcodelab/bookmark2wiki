<HTML>
<style>
    .buttonblock {
  display: block;
  width: 100%;
  border: none;
  background-color: #04AA6D;
  padding: 14px 28px;
  font-size: 16px;
  cursor: pointer;
  text-align: center;
}
    
</style>
<HEAD>
<?php
        /**
     * bookmark2wiki
	 * forked from post2wiki.php by riny [at] bk [dot] ru
	 * To bookmark webpage using bookmarklet
     * The app will add the url, title and hightlighed text you want to the end of the content of the targeted namespace. It does not directly read/white the dokuwiki page folder.
	 * Version 0.1 
	 * todo : check security of input
         * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
         * @author     edwardcodelab https://github.com/edwardcodelab   
         **/
 
	// TYPICAL USAGE :     
        //                 Create bookmarklet as shown in the bookmarklet part below: 
        //                 Change the window.open statement to reflect the location of the bookmark2wiki.php script.
        //                 Drag bookmarklet to your toolbar.
        //  BOOKMARKLET : 
	//  javascript:Q=document.selection?document.selection.createRange().text:document.getSelection(); void(window.open('https://myserver/bookmark2wiki.php?te='+escape(Q)+'&ur='+ escape(location.href)+'&ti='+escape(document.title),'dokuwikiadd','scrollbars=yes,resizable=yes,toolbars=yes,width=200,height=100,left=200,top=200,status=yes'));
 
	// SETUP SECTION
	$namespace="new_bookmarks";  // default namespace for weblog
	$host="https://yourhost.com";    // servername where your dokuwiki is
	$dokuwikipath="/dokuwiki/doku.php"; //path to your dokuwiki
 
	// POST TO WIKI
	$timestamp = date("Y:m:d:H:i:s"); //Group blogged items per year
	$wikitext=$_GET['te'];  // things to log : Selected text
	$url=$_GET['ur'];       // things to log : URL
	$title=$_GET['ti'];     // things to log : title
	$string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $title); // convert the unicode
    $title = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
    $string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $wikitext); // convert the unicode
    $wikitext = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
	$targeturl="https://$host$dokuwikipath?id=$namespace&do=edit";    
	
	      echo '<script>';
          echo 'function loadFunc(){';
          echo 'document.getElementById("top").contentWindow.document.getElementsByTagName("textarea")[0].innerHTML += "    * [['.$url.'|'.$title.']] \\\\\\\\ '.$wikitext.' -- '.$timestamp.'\r\n";';

          echo '};';
          echo '</script>';

  ?>

</HEAD>
<BODY onload="loadFunc();">
<button id="close_button" class="buttonblock" onclick='window.close();'>CLOSE</button>
 <?php
echo '<iframe src="'.$targeturl.'" id="top" width="100%" height="100%"></iframe>';
    ?>
</BODY>

</HTML>