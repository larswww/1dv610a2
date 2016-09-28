<?php
namespace view;

class LayoutView {
  
  public function render($isLoggedIn, LoginView $v, DateTimeView $dtv) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $v->response() . '
              
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }

    /**
     * @param $isLoggedIn
     * @return string
     */
    private function renderIsLoggedIn($isLoggedIn) {
        $isLogged = $_SESSION['isLoggedIn'] ?? false;

        if ($isLogged) {
      return '<h2>Logged in</h2>';
    }
    else {
        if ($_SERVER["QUERY_STRING"] === "register") {
            $message = "<a href='?'>Back to login</a>";
        } else {
            $message = "<a href='?register'>Register a new user</a>";
        }

      return $message . "<h2>Not logged in</h2>";
    }
  }
}
