<?php

if(!function_exists('panel')) return;

panel()->routes(array(
  array(
    'pattern' => 'pages/(:any)/add',
    'action'  => function($id) use($template) {
      $panel  = panel();
      $parent = $panel->page($id);

      // call the default controller
      if($parent->intendedTemplate() !== '[template-name]') {
        require_once $panel->roots()->controllers() . DS . 'pages.php';
        $controller = new PagesController();
        return $controller->add($id);
      }

      $controller = new Kirby\Panel\Controllers\Base();

      if($parent->ui()->create() === false) {
        throw new PermissionsException();
      }

      $form = $panel->form(__DIR__ . DS . 'form.php', $parent, function($form) use($parent, $controller) {
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
