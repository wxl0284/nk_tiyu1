<?php
/**
 * tp-mailer [A powerful and beautiful php mailer for All of ThinkPHP and Other PHP Framework based SwiftMailer]
 *
 *
 * @link      https://github.com/yuan1994/tp-mailer
 *
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . DIRECTORY_SEPARATOR . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');

require_once  __DIR__ . '/../../swiftmailer/lib/swift_required.php';
