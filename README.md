# NodePHP
The PHP instance of Project-Level Node JS. 

This library allows you to install node js on your project making it possible for you to use node js even when it is not installed in the system. Moreover, You can consume the power of node js, npm and npx commands with ease using a php class.

[Read Blog Post.](https://www.shade.codes/introducing-project-level-node-js/)

## Installation
NodePHP is available on Packagist and installation via Composer is the recommended way to install NodePHP.

```
composer require abhishek6262/nodephp
```

## Examples
```
require_once "vendor/autoload.php";

// $environment = new \abhishek6262\NodePHP\System\Environment('projectRootPath', 'binDirectoryPath');
$environment = new \abhishek6262\NodePHP\System\Environment(__DIR__);

$npm = new \abhishek6262\NodePHP\NPM($environment);

if (! $npm->exists()) {
    $npm->install();
}

if ($npm->packagesExists() && ! $npm->packagesInstalled()) {
    $response = $npm->installPackages();
    
    if ($response->statusCode() == '0') {
        echo "Packages successfully installed.";
    } else {
        echo "Failed to install the packages.";
    }
    
    print_r($response->output());
}
```

## Credits

- [Abhishek Prakash](https://github.com/abhishek6262)

## Contributing
Please feel free to fork this package and contribute by submitting a pull request to enhance the functionality. I will appreciate that a lot. Also please add your name to the credits.

Kindly [follow me on twitter](https://twitter.com/_the_shade)!

## Support
Moreover, To keep this and my other open source projects ongoing You can also support me on Patreon by clicking on the button below.

[<img src="https://c5.patreon.com/external/logo/become_a_patron_button.png">](https://www.patreon.com/bePatron?u=5563585)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.