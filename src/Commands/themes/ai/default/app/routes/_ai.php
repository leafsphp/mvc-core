<?php

app()->get('/ai', 'Ai\ChatController@show');
app()->post('/ai/chat', 'Ai\ChatController@chat');
