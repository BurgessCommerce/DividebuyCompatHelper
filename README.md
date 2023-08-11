# Provide Integration between Hyva_DividebuyPayment with Hyva_CompatModuleFallback and Hyva_ThemeFallback

Covered scenario(s)
- on checkout fallback theme, requests from fallback page to other urls which are not listed in `Apply fallback to requests containing` still trigger hyva theme.
- we only need this logic to work on checkout page currently


Note(s)
- this approach uses referer url in request object to determine if request was sent from checkout page
