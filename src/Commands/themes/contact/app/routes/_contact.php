<?php

app()->get('/contact', 'Contact\ContactController@show');
app()->post('/contact', 'Contact\ContactController@submit');
