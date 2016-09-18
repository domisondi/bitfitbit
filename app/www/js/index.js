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
        $('#list').html('');
        $.each(data.collections, function(index, object) {
            i++;
            $("#list").append("<a class='collection col-6' href='#' id='" + object.id + "'>" + "<span id='" + object.id + "'>#" + (i).toString() + "</span><h3 id='" + object.id + "'>" +
                object.name + "</h3><i id='" + object.id + "'>" + object.description + "</i></a>");
            $("#" + object.id).css('background-color', colors[i % colors.length]);});
        app.viewPage('collections');
    }
};

var current_collection_id = -1;
$('#list').on("click", '.collection', function(e) {
    e.preventDefault();
    
    if(!e.target.id) return;
    
    // set current_collection_id
    current_collection_id = e.target.id;
    
    $('#collections').hide();

    // fuck item list to death
    $('#item-list').html('');

    // create items
    $.each(data.collections[current_collection_id].items, function(index, object) {
        var achieved = '';
        var purchaseBtn = '';
        if(object.completed) achieved = 'achieved';
        
        var button_ev_disabled = '';
        if(object.nr_steps > avail_steps || object.completed) { //avail_steps
            button_ev_disabled = 'disabled';
        }
        purchaseBtn = '<button class="item-button" '+button_ev_disabled+' id="'+object.id+'">Purchase</button>';
        $('#item-list').append('<li class="item-element '+achieved+'"><h4 class="item-name">' + object.name +'</h4>'+
                            '<p class="item-description">'+
                                object.description +
                                '<span class="item-goal"><img src="img/goal.png" />'+ object.nr_steps +' Steps</span>'+
                                '<span class="item-prize"><img src="img/award.png" /> '+ object.reward +'</span>'+purchaseBtn+
                            '</p></li>');
    });

    $('#collection-name').html(data.collections[current_collection_id].name);
    $('#collection-description').html(data.collections[current_collection_id].description);

    // show back button
    $('#back-button').show();

    $('#items').show();
    window.scrollTo(0,0);
});

$('#item-list').on("click", '.item-button', function(e) {
    e.preventDefault();
    
    var current_item_id =  e.target.id;
    
    $.ajax({
        dataType: 'json',
        url : serverUrl + 'api/?request=complete&user_id='+userId+'&access_token='+token+'&item_id='+ current_item_id +'&coll_id='+ current_collection_id
    }).done(function(dataT) {
        if(dataT.status==0){
            console.log("Done... Purchase completed:\n" + JSON.stringify(dataT.collections));
            $('#items').hide();
            avail_steps = dataT.step_count;
            data.collections = dataT.collections;
            update_nr_available_steps_display();
            alert('Purchase completed.');
            $('#purchase-hash').val(Math.random() * 0x10000000000000000 + 1);
            $('#purchase').show();
            window.scrollTo(0,0);
        }
        else {
            console.log("Done... Purchase failed:\n" + dataT.err_msg);
            alert('Purchase failed: '+dataT.err_msg);
        }
    });
});

$('#back-button').on('click', function() {
    $('#items').hide();
    $('#purchase').hide();  
    $('#collections').show();
    current_collection_id = -1;
    window.scrollTo(0,0);
    
    // hide back button
    $('#back-button').hide();
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
    $('#available-steps').html("Fitcoins: " + avail_steps);
}
