This version of the custom_error_page can only be used in >= TYPO3 8 LTS.

## How to use

There are up to three steps to take:

- Set up the YAML with all your domains for each page root tree and their pids to their specific 404/503 page. An example
  file is in the folder "Examples" of this extension. The default file destination is "typo3conf/custom_error_conf.yml".

- Change the value of [FE][pageNotFound_handling] to "USER_FUNCTION:Bitmotion\CustomErrorPage\Utility\CustomErrorPageUtility->showCustom404Page" (without "")
- Change the value of [FE][pageUnavailable_handling] to "USER_FUNCTION:Bitmotion\CustomErrorPage\Utility\CustomErrorPageUtility->showCustom503Page" (without "")

There is a configuration example in <code>Resources/Private/Examples</code>.


## Configuration

Full example:

```
configuration: &default
  name: www.domain.tld
  pages:
    404:  29
    503:  12
  language-pattern:
    default: true
    en: 0
    de: 1
    fr: 2
  additional-tlds:
    - tld: .foo
    - tld: .bar
    - tld: .foo.bar
  https: true


404: &404
  - domain:
      <<: *default

503: &503
  <<: *404
```

### 404 / 503
Contains the configuration array for handling 404 or 503 errors. Both keys contain an array of domains which does have the following configuration possibilities:

+ <code>name</code> (string): The full name of the domain
+ <code>https</code> (bool): True if SSL is used
+ <code>pages</code> (array): Contains configuration for pages
+ <code>additional-tlds</code> (array): Contains further TLD for configured domain
+ <code>language-pattern</code> (array): Contains configuration for different languages

#### pages
The pages array does have to options:

+ <code>404</code> (int): The ID of the page which should be shown when an 404 error occurs
+ <code>503</code> (int): The ID of the page which should be shown when an 503 error occurs

#### additional-tlds
This array contains configuration for further top-level-domains. Each domain will be prepended to the configured name. In our examples this would be www.domain.tld.foo, www.domain.tld.bar and www.domain.tld.foo.bar.

+ <code>tld</code> (string): Additional domain suffix

Also possible:

```
configuration: &default
  name: www.domain
  [...]
  additional-tlds:
    - tld: .com
    - tld: .de
    - tld: .fr
```

#### language-pattern
Additional configuration for multi language sites. This array contains an mapping of the ISO-code and the corresponding sys_language_uid.

In our example we do have three languages configured: en, de and fr. The domain should be available under www.domain.tld/en/, www.domain.tld/de/ and www.domain.tld/fr/.

If there is a sys_language_uid which is not configured in one of the given patterns (for example 'es'), you will get an exception. Prevent that by using the <code>default</code> key and set the value to <code>true</code>.

+ <code>ISO-Code</code> (int): Generic key and value (see above)
+ <code>default</code> (bool): see above
