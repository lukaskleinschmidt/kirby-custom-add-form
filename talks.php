<?php

if(!function_exists('panel')) return;

panel()->routes(array(
  array(
    'pattern' => 'pages/talks/add',
    'action'  => function() {

      $controller = new Kirby\Panel\Controllers\Base();
      $parent     = panel()->page('talks');

      if($parent->ui()->create() === false) {
        throw new PermissionsException();
      }

      $form = panel()->form(__DIR__ . DS . 'form.php', $parent, function($form) use($parent, $controller) {
        try {

          $form->validate();

          if(!$form->isValid()) {
            throw new Exception(l('pages.add.error.template'));
          }

          $data = $form->serialize();
          $page = $parent->children()->create($data['uid'], $data['template'], array(
            'title' => $data['title']
          ));

          $controller->notify(':)');
          $controller->redirect($page, 'edit');

        } catch(Exception $e) {
          $form->alert($e->getMessage());
        }
      });

      $content = tpl::load(__DIR__ . DS . 'template.php', compact('form'));

      return $controller->layout('app', compact('content'));
    },
    'filter' => 'auth',
    'method' => 'POST|GET',
  )
));
