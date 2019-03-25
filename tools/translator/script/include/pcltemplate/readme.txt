// -----------------------------------------------------------------------------
// PclTemplate - readme.txt
// -----------------------------------------------------------------------------
// License GNU/LGPL - November 2006
// Vincent Blavet - vincent@phpconcept.net
// http://www.phpconcept.net
// -----------------------------------------------------------------------------
// $Id$
// -----------------------------------------------------------------------------


Introduction
============

  PclTemplate is a simple and easy template management class.
  PclTemplate allow to read template files or template strings and generate
  results from a simple structure. The result can be a file, a string, or can be
  directly send to the output.
  
  PclTemplate allow the configuration of the tokens delimiters.

  Full documentation about PclTemplate will be there in a near future (I hope) :
  http://www.phpconcept.net/pcltemplate

What's new
==========

  Version 0.5 :
    - Add support for including template files in template, by using
      keyword 'include'.
    - Add some support for error reporting in template parsing.
    
  Version 0.4 :
    - Support for nested blocks (if inside list, list inside if, ...)
    - 'ifnot' bug correction.
      
  Version 0.3 :
    - Support for similar start and end delimiters
      
  Version 0.2 :
    - Support for simple 'if' condition bloc :
      Inside the if condition bloc the same name can be reused for a token.
      By doing that the bloc will be displayed only if the value is set.
      
  Version 0.1 :
    - Support for customisable delimiters
    - Support for string template
    - Support for file template
    - Start of the class.


Known bugs or limitations
=========================

  - Template is loaded in memory. If the template is large, be aware of the
    PHP memory limitation of your system. This value can be configured in
    php.ini file. (see : http://www.php.net/ini.core)

License
=======

  PclTemplate Class is released under GNU/LGPL license.
  This library is free, so you can use it at no cost.

  HOWEVER, if you release a script, an application, a library or any kind of
  code using PclTemplate library (or a part of it), YOU MUST :
  - Indicate in the documentation (or a readme file), that your work
    uses PclTemplate Class, and make a reference to the author and the web site
    http://www.phpconcept.net
  - Gives the ability to the final user to update the PclTemplate libary.

  I will also appreciate that you send me a mail (vincent@phpconcept.net), just 
  to be aware that someone is using PclTemplate.

  For more information about GNU/LGPL license : http://www.gnu.org

Warning
=======

  This class and the associated files are non commercial, non professional work.
  It should not have unexpected results. However if any damage is caused by
  this software the author can not be responsible.
  The use of this software is at the risk of the user.

Documentation
=============

  PclTemplate User Manuel will be available in http://www.phpconcept.net in
  a near future.

Author
======

  This software was written by Vincent Blavet (vincent@phpconcept.net) on its
  leasure time.

Contribute
==========

  If you want to contribute to the development of PclTemplate, please contact
  vincent@phpconcept.net.
  If you can help in financing PhpConcept hosting service, please go to
  http://www.phpconcept.net/soutien.en.php
