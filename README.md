Data Type RDF (module for Omeka S)
==================================

> __New versions of this module and support for Omeka S version 3.0 and above
> are available on [GitLab], which seems to respect users and privacy better.__

[Data Type RDF] is a module for [Omeka S] that implements some [RDF datatypes]
and an [XSD datatype] recommended by the World Wide Web consortium [W3C] in
order to simplify user input and to give a better semanticity to the values of
the properties.

Added rdf and xsd datatypes:
- [`rdf:HTML`](https://www.w3.org/TR/rdf11-concepts/#section-html): an html fragment
- [`rdf:XMLLiteral`](https://www.w3.org/TR/rdf11-concepts/#section-XMLLiteral): an xml fragment
- [`xsd:boolean`](https://www.w3.org/TR/xmlschema11-2/#boolean): true or false

Number and date time data types can be managed with module [Numeric Data Types].


Installation
------------

Uncompress files and rename plugin folder `DataTypeRdf`.

See general end user documentation for [Installing a module] and follow the
config instructions.


Usage
-----

The data types are automatically available through the resource templates. It is
not recommended to use too many data types by property. One to four types are
enough in most of the cases.


TODO
----

- Simplify search ([Omeka S issue #1241]).
- Add xsd:token or a derivative for standard or custom enumerations (language, etc.).
- Add xsd:anyURI for uris.
- Manage restrictions via the resource templates (default value for boolean,
  range, default tokens, with or without seconds, css for html, xsl for xml…).


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitLab.


License
-------

This module is published under the [CeCILL v2.1] license, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

This software is governed by the CeCILL license under French law and abiding by
the rules of distribution of free software. You can use, modify and/ or
redistribute the software under the terms of the CeCILL license as circulated by
CEA, CNRS and INRIA at the following URL "http://www.cecill.info".

As a counterpart to the access to the source code and rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors have only limited liability.

In this respect, the user’s attention is drawn to the risks associated with
loading, using, modifying and/or developing or reproducing the software by the
user in light of its specific status of free software, that may mean that it is
complicated to manipulate, and that also therefore means that it is reserved for
developers and experienced professionals having in-depth computer knowledge.
Users are therefore encouraged to load and test the software’s suitability as
regards their requirements in conditions enabling the security of their systems
and/or data to be ensured and, more generally, to use and operate it in the same
conditions as regards security.

The fact that you are presently reading this means that you have had knowledge
of the CeCILL license and that you accept its terms.


Copyright
---------

* Copyright Daniel Berthereau, 2018-2020


[Data Type RDF]: https://gitlab.com/Daniel-KM/Omeka-S-module-DataTypeRdf
[Omeka S]: https://omeka.org/s
[Numeric Data Types]: https://github.com/omeka-s-modules/NumericDataTypes
[RDF datatypes]: https://www.w3.org/TR/rdf11-concepts/#section-Datatypes
[XSD datatype]: https://www.w3.org/TR/xmlschema11-2
[W3C]: https://www.w3.org
[installing a module]: http://dev.omeka.org/docs/s/user-manual/modules/#installing-modules
[Omeka S issue #1241]: https://github.com/omeka/omeka-s/issues/1241
[module issues]: https://gitlab.com/Daniel-KM/Omeka-S-module-DataTypeRdf/-/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[MIT]: https://github.com/sandywalker/webui-popover/blob/master/LICENSE.txt
[GitLab]: https://gitlab.com/Daniel-KM
[Daniel-KM]: https://gitlab.com/Daniel-KM "Daniel Berthereau"
