Fixed Width
===========

Purpose
-------
The fixed width library's purpose is to make working with fixed width files a bit
easier. Includes things like easily accessing line and field indexes and using a
file spec to allow for setting a loading value using field names instead of indexes
and ranges

Usage
-----

### Basic ###

for real simple fixed widht file work you can create a new Instance of `File` and add
lines to it setting indexes on ines and adding lines

```php
<?php

use Giftcards\FixedWidth\File;
use Giftcards\FixedWidth\Line;

$file = new File(
    'name' /* the name of the file required */,
    20 /* width required */,
    array() /* initial lines if you have any defaults to an empty array */,
    "\r\n" /* line seperator defaults to \r\n */
);

$line1 = $file->newLine(); //this instanciates a new line and adds it to the file
                        //then returns it to be edited
$line1[2] = 'w'; //the line class follows ArrayAccess so can be used as an array to set chars
$line1['2:3'] = 'w'; //the index can also be a string with 2 numbers seperated by
                 //a colon to denote a range this is the equivelant range to the above
                 //index it set the value from and including index a until and exlcuding
                 //index b
                 //you can also get a value using an index or range.
                 //you can also pass an instance of Slice which is what it is converted
                 //to internally
var_dump('w' == $line[new Slice(2, 3)]);//this will be true
$line1['3:5'] = 'er';//so index 3 and 4 will be set with this value.
$line2 = new Line($file->getWidth()); //you can also create a line
$file[] = $line2; //and add it to the file. it follows ArrayAccess as well
               //you can also use addLine for the same effect
$file[1] = $line2; //this has the same effect and setLine(1, $line2) can be used as well
unset($file[0]); //this removes the line at that index and reindexes the lines
              //so $file[0] === $line2
$line2['0:5'] = 'hello';
$line2['6:11'] = 'world'; //$line2 now says 'hello world         '
echo $line2; //the Line class has a __toString method so this will print out the contents
$file[] = $line1;
echo $file; //this will echo out the whole file line by line separated by the line

```

the output of the last echo will look something like this

```
hello world
  wer
```

### File Factory ###

instead of instantiating the file class yourself you can use the file factory
to do it the base file factory gives you 2 methods

 - create - convenience method to just create a file
 - createFromFile - this can be pass an instance of SplFileInfo to create the file from.

### Specs ###

usually when working with fixed width files you don't want to have to remember
which index different fields are in. to accomplish this the fixed width library
comes with a way to define record specs. a record spec is a spec that defines what
a line of a certain type should look in a file. often a file will have a few different types
of records the spec system allows you to define those specs each as their own record spec
and the fields that are in them. you can have record specs either as a php array
or as a yaml file at this point but it's pretty easy to define a new spec loader
it needs to follow the `Giftcards\FixedWidth\Spec\Loader\SpecLoaderInterface` there
is an abstract class that make it easy to define loaders that load form the file system
`AbstractSpecFileLoader`.

an example spec can be seen in [Tests/Fixtures/spec.yml](Tests/Fixtures/spec.yml).

#### Spec options ####

at the file level you can define 2 direct options.

- width - the width the file and all lines generated form this spec should have
- line_separator - the string that should be used as a line separator. defaults to \r\n

after that the main definition is centered around records which have fields. fields have
these options.

- default - this will the default value supplied if the value isn't sent in for this field
            if this is missing the field is automatically required and will throw an exception
            when not sent in
- slice - this is the range/index in the line this value should be set for
- format_specifier - this is the actual specifier to be used when formatting the
                     value generally this is the same char that is used in sprint
                     for example s or .2f
- padding_char - this is the character that should be used for padding the missing
                 space between the value and the full field length. the default is
                 a space
- padding_direction - this is the side the padding should be put on the valid options
                       are left or right
- type - this is the type the field has. this will be explained next

#### field types ####

very often many fields happen to share allot of the above options in common this is
why when defining a spec definition you can use types to define the shared properties
then set the field's type and it will inherit all options set for that type. see the above mentioned
example spec for some more details

#### Building a file ####

to build a file using a spec you will need to define a spec loader. after that the
best way to do it is instantiate an instance of `Giftcards\FixedWidth\Spec\FileFactory`
this inherits from the base factory class mentioned above and adds some methods for
dealing with specs. it requires the spec loader to do it's thing.

```php
<?php

use Giftcards\FixedWidth\Spec\FileFactory;
use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Symfony\Component\Config\FileLocator;


$factory = new FileFactory(new YamlSpecLoader(new FileLocator(__DIR__.'/Tests/Fixtures/')));
//instantiate a builder
$builder = $factory->createBuilder('fileName', 'spec1');
$builder
    ->addRecord('record1', array(
        //field1 isnt required since it has a default
        'field2' => 'go'
    ))
    ->addRecord('record1', array(
        'field1' => 2.3,
        'field2' => 'hi'
    ))
    ->addRecord('record2', array(
        'field3' => 12345
    ))
;
echo $builder->getFile();
```
this will output

```
                                  23.34 go
                                  x2.30 hi
                                                   12345
```

#### Reading a file ####

you can also use the spec to read already built files. say you saved the above output
to a file you can load it and read the fields or read a whole line to an array

```php
<?php

use Giftcards\FixedWidth\Spec\FileFactory;
use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Symfony\Component\Config\FileLocator;

$file = new \SplFileInfo('path');

$factory = new FileFactory(new YamlSpecLoader(new FileLocator(__DIR__.'/Tests/Fixtures/')));


$reader = $factory->createReader($factory->createFromFile($file), 'spec1');

$field1 = $reader->parseField(
    0 /* index of the line you want to read */,
    'field1' /* name of the field you want to read */,
    'record1' /* record spec name to use */
);

var_dump($field1); //output will be double(23.34)

$record = $reader->parseLine(0, 'record1');
var_dump($record); //output will be
/*
array(2) {
  'field1' =>
  double(23.34)
  'field2' =>
  string(2) "go"
}
*/
```
