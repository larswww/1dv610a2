<?php
namespace view;

use model\GateKeeper;

require_once('./model/GateKeeper.php');

class LayoutView
{

    public function render(GateKeeper $gk, LoginView $v, DateTimeView $dtv)
    {
        echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn($gk) . '
          
          <div class="container">
              ' . $v->getResponse() . '
              
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
    private function renderIsLoggedIn(GateKeeper $gk)
    {
        $isLoggedIn = $gk->getIsLoggedIn();

        if ($isLoggedIn) {
            return '<h2>Logged in</h2>';
        } else {
            if ($_SERVER["QUERY_STRING"] === "register=1" || isset($_REQUEST["register"])) {
                $message = "<a href='?'>Back to login</a>";
            } else {
                $message = "<a href='?register=1'>Register a new user</a>";
            }

            return $message . "<h2>Not logged in</h2>";
        }
    }
}
