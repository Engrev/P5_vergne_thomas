<?php
namespace App\Controllers;

class PostsController
{
    public function show($id, $slug, $page = null) {
        if (is_null($page)) {
            echo "Je suis l'article $id.";
        } else {
            echo "Je suis l'article $id à la page $page.";
        }
    }
}