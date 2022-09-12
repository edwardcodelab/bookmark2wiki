# bookmark2wiki
to send bookmark from browser to dokuwiki
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
