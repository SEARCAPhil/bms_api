


# BMS API  
Restful services for [Bidding Management System](https://github.com/SEARCAPhil/bidding_management_system)   

![development](https://img.shields.io/badge/stage-development-lightgrey.svg)

> This API is currently in the development stage and the following endpoints might change in the future without prior notice.
You may use the latest API in the `develop` branch only for testing and debugging new features.For your comments and suggestions, submit your ticket [here](https://github.com/SEARCAPhil/bms_api/issues).  

## Installation
1. `git clone https://github.com/SEARCAPhil/bms_api.git`
2. Import `assets/SQl/bms.sql` to your DBMS
3. Change `src/config/database/config.php` with your database configuration. 
4. Open [http://localhost/bms_api/src/api/suppliers/](http://localhost/bms_api/src/api/suppliers/) with your preferred browser.
If you see a `JSON` response on the screen like the code below , API is now working on your server.
	```javascript
	{"data":[{"id":"1","name":"SEARCA","tagline":"Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA)","about":"The Southeast Asian Regional Center for Graduate Study and Research in Agriculture (SEARCA) is a non-profit organization established by the Southeast Asian Ministers of Education Organization (SEAMEO) in 1966.\r\n\r\nFounded in 1965, SEAMEO is a chartered international organization whose purpose is to promote cooperation in education, science and culture in the Southeast Asian region. Its highest policymaking body is the SEAMEO Council, which comprises the Ministers of Education of the 11 SEAMEO Member Countries, namely: Brunei Darussalam, Cambodia, Indonesia, Lao PDR, Malaysia, Myanmar, the Philippines, Singapore, Thailand, Timor-Leste, and Vietnam.\r\n\r\nSEAMEO also has Associate Member Countries, namely: Australia, Canada, France, Germany, Netherlands, New Zealand, Spain, and the United Kingdom.\r\n\r\nThe Center derives its juridical personality from the SEAMEO Charter and possesses full capacity to contract; acquire, and dispose of, immovable and movable property; and institute legal proceedings. Moreover, SEARCA enjoys in the territory of each of its member states such privileges and immunities as are normally accorded United Nations institutions. Representatives of member states and officials of the Center shall similarly enjoy such privileges and immunities in the Philippines as are necessary for the exercise of their functions in connection with SEARCA and SEAMEO.","established_month":null,"established_date":null,"established_year":"1960","location":"UPLB","industry":"Agriculture","logo":"http:\/\/www.searca.org\/images\/SEARCA-web-logo.png","status":"0","date_created":"2017-10-17 11:06:35"}]}
	```   
5. Please Read the [API](#api) documentation for easy use.



## API 

#### Suppliers
- [Suppliers](docs/suppliers/API.md)   
- [Suppliers\Accounts](docs/suppliers/accounts/API.md)
	- [Suppliers\Accounts\Logs](docs/suppliers/accounts/logs/API.md)
- [Suppliers\Products](docs/suppliers/accounts/API.md)
	- [Suppliers\Products\Categories](docs/suppliers/products/categories/API.md)
	- [Suppliers\Products\Prices](docs/suppliers/products/prices/API.md)
	- [Suppliers\Products\Specifications](docs/suppliers/products/specifications/API.md)
	- [Suppliers\Products\Templates](docs/suppliers/products/templates/API.md)    



