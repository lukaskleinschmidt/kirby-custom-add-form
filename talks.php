<?php

panel()->routes(array(
  array(
    'pattern' => 'pages/talks/add',
    'action'  => function() {

      $controller = new Kirby\Panel\Controllers\Base();
      $parent     = panel()->page('talks');
      $self       = $this;

      if($parent->ui()->create() === false) {
        throw new PermissionsException();
      }

      // somehow the reference to the form only works this way
      // __DIR__ . DS . 'form.php' does not seem to work

      $form = $parent->form(str_repeat('..' . DS, 4) . 'site' . DS . 'plugins' . DS . 'talks' . DS . 'form', function($form) use($parent, $self) {

        try {

          $form->validate();

          if(!$form->isValid()) {
            throw new Exception(l('pages.add.error.template'));
          }

          $data = $form->serialize();
          $page = $parent->children()->create($data['uid'], $data['template'], array(
            'title' => $data['title']
          ));

          $self->notify(':)');
          $this->redirect($page, 'edit');

        } catch(Exception $e) {
          $form->alert($e->getMessage());
        }
      });

      $content = new Brick('div');
      $content->html($form);
      $content->addClass('modal-content modal-content-medium');

      return $controller->layout('app', compact('content'));
    },
    'filter' => 'auth',
    'method' => 'POST|GET',
  )
));
