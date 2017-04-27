This version of the custom_error_page can only be used in >= TYPO3 8 LTS.

## How to use

There are up to three steps to take:

- Set up the YAML with all your domains for each page root tree and their pids to their specific 404/503 page. An example
  file is in the folder "Examples" of this extension. The default file destination is "typo3conf/custom_error_conf.yml".

- Change the value of [FE][pageNotFound_handling] to "USER_FUNCTION:Bitmotion\CustomErrorPage\Utility\CustomErrorPageUtility->showCustom404Page" (without "")
- Change the value of [FE][pageUnavailable_handling] to "USER_FUNCTION:Bitmotion\CustomErrorPage\Utility\CustomErrorPageUtility->showCustom503Page" (without "")