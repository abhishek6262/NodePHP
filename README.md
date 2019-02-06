# NodePHP
The PHP instance of Project-Level Node JS. 

This library allows you to install node js on your project making it possible for you to use node js even when it is not installed in the system. Moreover, You can consume the power of node js, npm and npx commands with ease using a php class.

## Examples
```
require_once 'vendor\autoload.php';

// $node = new \abhishek6262\NodePHP\NodePHP('pathToProjectRoot', 'pathToBinDirectory');
$node = new \abhishek6262\NodePHP\NodePHP(__DIR__);

if (! $node->exists()) {
    $node->install();
}

if ($node->npmPackagesExists() && ! $node->npmPackagesInstalled()) {
    $node->installNpmPackages();
}
```

## Credits

- [Abhishek Prakash](https://github.com/abhishek6262)

## Contributing
Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities. I will appreciate that a lot. Also please add your name to the credits.

Kindly [follow me on twitter](https://twitter.com/_the_shade)!

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.