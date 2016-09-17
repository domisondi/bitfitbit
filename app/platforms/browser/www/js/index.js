/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
var serverUrl = 'http://bitfit.dominicsonderegger.ch/'; 

var data;

var app = {

    // Application Constructor
    initialize: function() {
        this.bindEvents();
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicitly call 'app.receivedEvent(...);'
    onDeviceReady: function() {
        app.authenticateUser();
    },
    viewPage: function(pageId) {
        $('.page').hide();
        $('#' + pageId).show();
    },
    authenticateUser: function() {
        $('.event.authenticating').css("display","inline-block");
        $.oauth2({
            auth_url: 'https://www.fitbit.com/oauth2/authorize',
            response_type: 'token',
            token_url: '',
            logout_url: '',
            client_id: '227Z6J',
            client_secret: '',
            redirect_uri: serverUrl + '?page=callback',
            other_params: {
                scope: 'activity',
                expires_in: 604800
            }
        }, function(token, response){
            $.urlParam = function(name, response){
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(response);
                return results[1] || 0;
            }
            var userId = $.urlParam('user_id', response);
            $('.event.authenticating').css("display","none");
            app.gatherOurData(token, userId);
            
        }, function(error, response){
            alert(response);
        });
    },
    gatherOurData: function(token, userId) {
        var output;
        $('.event.loading').css("display","inline-block");
        console.log("Trying to connect to server @" + serverUrl 
            + 'api/?request=items&access_token=' + token 
            + '&user_id=' + userId);
        $.ajax({
            url: serverUrl + 'api/?request=items&access_token=' 
            + token + '&user_id=' + userId
        }).done(function( dataT ) {
            console.log("Done... Data gathered:\n" + dataT);
            data = jQuery.parseJSON(dataT);
            app.outputCollections();
        });
        $('.event.loading').css("display","none");
    },
    
    outputCollections: function() {
        $.each(data.collections, function(index, object) {
            $("#list").append("<a class='col-6' href='#' id='n26'>" 
                + object.name +"</a>")});
        app.viewPage('collections');
    }
};

$('a.col-6').on("click", function(e) {
    e.preventDefault();
    alert($(e.target).attr("id"));
});