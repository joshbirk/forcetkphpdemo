<!DOCTYPE html>
<!--
Copyright (c) 2011, salesforce.com, inc.
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided
that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the
 following disclaimer.

Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
the following disclaimer in the documentation and/or other materials provided with the distribution.

Neither the name of salesforce.com, inc. nor the names of its contributors may be used to endorse or
promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
-->
<!--
Sample HTML page showing use of Force.com JavaScript REST Toolkit from
an HTML5 mobile app using jQuery Mobile
-->
<html>
<head>
<title>Accounts</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--
    For development, you may want to load jQuery/jQuery Mobile from their CDN. 
-->
<link rel="stylesheet" href="jquery.mobile-1.3.0.min.css" />
<script type="text/javascript" src="jquery.min.js"></script>
<!--
From jQuery-swip - http://code.google.com/p/jquery-swip/source/browse/trunk/jquery.popupWindow.js 

<script type="text/javascript" src="static/jquery.popup.js"></script>
-->

<script type="text/javascript" src="jquerymobile.js"></script>
<script type="text/javascript" src="forcetk.js"></script>
<script type="text/javascript" src="mobileapp.js"></script>
<script type="text/javascript">
// OAuth Configuration
var loginUrl    = 'https://login.salesforce.com/';
var clientId    = '3MVG9A2kN3Bn17htFsz.Zr8IKNPRTz3FiPC7xCYILGF8XaGsUKX44CelH0LkqEC0GaOdrZk_Mkw5rM.mMtWmJ'; //demo only
var redirectUri = 'https://forcetkphpdemo.herokuapp.com/oauthcallback.html';
var proxyUrl    = 'https://forcetkphpdemo.herokuapp.com/proxy.php?mode=native';

// We'll get an instance of the REST API client in a callback after we do 
// OAuth
var client = new forcetk.Client(clientId, loginUrl, proxyUrl);;

// We use $j rather than $ for jQuery
if (window.$j === undefined) {
    $j = $;
}

$j(document).ready(function() {
	$j('#login').popupWindow({ 
		windowURL: getAuthorizeUrl(loginUrl, clientId, redirectUri),
		windowName: 'Connect',
		centerBrowser: 1,
		height:480, 
		width:320
	});
});

function getAuthorizeUrl(loginUrl, clientId, redirectUri){
    return loginUrl+'services/oauth2/authorize?display=touch'
        +'&response_type=token&client_id='+escape(clientId)
        +'&redirect_uri='+escape(redirectUri);
}

function sessionCallback(oauthResponse) {
    if (typeof oauthResponse === 'undefined'
        || typeof oauthResponse['access_token'] === 'undefined') {
        //$j('#prompt').html('Error - unauthorized!');
        errorCallback({
            status: 0, 
            statusText: 'Unauthorized', 
            responseText: 'No OAuth response'
        });
    } else {
        client.setSessionToken(oauthResponse.access_token, null,
            oauthResponse.instance_url);

		addClickListeners();

	    $j.mobile.changePage('#mainpage',"slide",false,true);
	    $j.mobile.pageLoading();
	    getRecords(function(){
	        $j.mobile.pageLoading(true);
	    });
    }
}
  </script>
</head>

<body>
	<div data-role="page" data-theme="b" id="loginpage">

	    <div data-role="header">
	        <h1>Login</h1>
	    </div>
	    <div data-role="content">
	        <form>
	            <button data-role="button" id="login">Login</button>
	        </form>
	    </div>
	    <div data-role="footer">
	        <h4>Force.com</h4>
	    </div>
	</div>
	<div data-role="page" data-theme="b" id="mainpage">

	    <div data-role="header">
	        <h1>Contacts</h1>
	    </div>
	    <div data-role="content">
	        <form>
	            <button data-role="button" id="newbtn">New</button>
	        </form>
	        <ul id="list" data-inset="true" data-role="listview" 
			  data-theme="c" data-dividertheme="b">
	        </ul>
	    </div>
	    <div data-role="footer">
	        <h4>Force.com</h4>
	    </div>
	</div>
	<div data-role="page" data-theme="b" id="detailpage">
	    <div data-role="header">
	        <h1>Contact Detail</h1>
	    </div>
	    <div data-role="content">
	        <table>
	            <tr><td>Name:</td><td id="Name"></td></tr>
	            <tr><td>Email:</td><td id="Industry"></td></tr>
	        </table>
	        <form name="accountdetail" id="accountdetail">
	            <input type="hidden" name="Id" id="Id" />
	            <button data-role="button" id="editbtn">Edit</button>
	            <button data-role="button" id="deletebtn" data-icon="delete" 
				  data-theme="e">Delete</button>
	        </form>
	    </div>
	    <div data-role="footer">
	        <h4>Force.com</h4>
	    </div>
	</div>
	<div data-role="page" data-theme="b" id="editpage">
	    <div data-role="header">
	        <h1 id="formheader">New Contact</h1>
	    </div>
	    <div data-role="content">
	        <form name="contact" id="form">
	            <input type="hidden" name="Id" id="Id" />
	            <table>
	                <tr>
						<td>Name:</td>
						<td><input name="Name" id="Name" data-theme="c"/></td>
					</tr>
	                <tr>
						<td>Email:</td>
						<td><input name="Email" id="Email" 
						  data-theme="c"/></td>
					</tr>
	            </table>
	            <button data-role="button" id="actionbtn">Action</button>
	        </form>
	    </div>
	    <div data-role="footer">
	        <h4>Force.com</h4>
	    </div>
	</div>
</body>
</html>
