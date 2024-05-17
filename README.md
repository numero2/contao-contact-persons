Contao Contact Persons Bundele
=======================

[![](https://img.shields.io/packagist/v/numero2/contao-contact-persons.svg?style=flat-square)](https://packagist.org/packages/numero2/contao-contact-persons) [![License: LGPL v3](https://img.shields.io/badge/License-LGPL%20v3-blue.svg?style=flat-square)](http://www.gnu.org/licenses/lgpl-3.0)

About
--

Manage contact persons for pages, news and events.

System requirements
--

* [Contao 4.13](https://github.com/contao/contao) (or newer)

Installation
--

* Install via Contao Manager or Composer (`composer require numero2/contao-contact-persons`)
* Run a database update via the Contao-Installtool or using the [contao:migrate](https://docs.contao.org/dev/reference/commands/) command.


Events
--

If you want to extend the contact persons using your own fields you can use the `contao.contact_person_parse` event to modify all the data that will be used in the templates.

```php
// src/EventListener/ContactPersonParseListener.php
namespace App\EventListener;

use Contao\ContactPersonsBundle\Event\ContactPersonEvents;
use Contao\ContactPersonsBundle\Event\ContactPersonParseEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(ContactPersonEvents::CONTACT_PERSON_PARSE)]
class ContactPersonParseListener {

    public function __invoke( ContactPersonEvents $event ): void {
        // …
    }
}
```