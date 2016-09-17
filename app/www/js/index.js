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
var token;
var userId;
var avail_steps = 0;

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
        }, function(token_t, response){
            token = token_t;

            $.urlParam = function(name, response){
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(response);
                return results[1] || 0;
            }
            userId = $.urlParam('user_id', response);
            $('.event.authenticating').css("display","none");
            app.gatherOurData();
            
        }, function(error, response){
            alert(response);
        });
    },
    gatherOurData: function() {
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

        update_nr_available_steps();

        $('.event.loading').css("display","none");
    },
    
    outputCollections: function() {
        var i = 0;
        var colors = ['#00a0b0', '#cc333f', '#eb6841', '#edc951'];
        $.each(data.collections, function(index, object) {
            i++;
            $("#list").append("<a class='collection col-6' href='#' id='" + object.id + "'>" + "<span>#" + (i).toString() + "</span><h3>" +
                object.name + "</h3><i id='" + object.id + "'>" + object.description + "</i></a>");
            $("#" + object.id).css('background-color', colors[i % colors.length]);});
        app.viewPage('collections');
    }
};

$('#list').on("click", '.collection', function(e) {
    e.preventDefault();
    $('#collections').hide();

    // fuck item list to death
    $('#item-list').html('');

    // create items
    $.each(data.collections[e.target.id].items, function(index, object) {
        var achieved = '';
        if(object.completed) achieved = 'achieved';
        
        $('#item-list').append('<li class="'+achieved+'"><h4 class="item-name">' + object.name +'</h4>'+
                            '<p class="item-description">'+
                                object.description +
                                '<span class="item-goal"><img src="img/goal.png" />'+ object.nr_steps +' Steps</span>'+
                                '<span class="item-prize"><img src="img/award.png" /> '+ object.reward +'</span>'+
                            '</p></li>');
    });

    $('#collection-name').html(data.collections[e.target.id].name);
    $('#collection-description').html(data.collections[e.target.id].description);

    $('#items').show();
    window.scrollTo(0,0);
});

$('#back-button').on('click', function() {
    $('#items').hide();
    $('#collections').show();
    window.scrollTo(0,0);
});

function update_nr_available_steps() {
    $.ajax({
        dataType: 'json',
        url: serverUrl + 'api/?request=stats&access_token=' 
        + token + '&user_id=' + userId
    }).done(function( dataT ) {
        console.log("Done... Stats gathered:\n" + dataT.stats.step_count);
        avail_steps = dataT.stats.step_count;
        update_nr_available_steps_display();
    });
}

function update_nr_available_steps_display() {
    $('#available-steps').html(avail_steps);
}
