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
      echo $file; //this will echo out the whole file line by line seperated by the line

```

the output of the last echo will look something like this

```

    hello world
      wer
```

### File Factory ###

instead of instanciating the file class yourself you can use the file factory
to do it the base file factory gives you 2 methods

 - create - convenience method to just create a file
 - createFromFile - this can be pass an instance of SplFileInfo to create the file from.

### Specs ###

usually when working with fixed width files you dont want to have to remember
which index different fields are in. to accomplish this the fixed width libray
comes with a way to define record specs
