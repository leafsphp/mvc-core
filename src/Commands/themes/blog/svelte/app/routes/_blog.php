<?php

app()->get('/blog', 'Blog\PostsController@index');
app()->get('/blog/{slug}', 'Blog\PostsController@show');
