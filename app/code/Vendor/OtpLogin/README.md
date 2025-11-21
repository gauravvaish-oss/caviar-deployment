Vendor_OtpLogin - Magento 2 module skeleton

Installation:
1. Copy the `Vendor/OtpLogin` folder to `app/code/Vendor/OtpLogin`.
2. Run:
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento cache:flush

Admin (Stores > Configuration > OTP Login > Twilio Settings):
  - Set Account SID, Auth Token, From Number, and Enable Twilio.

GraphQL:
  mutation {
    requestOtp(input: { identifier: "9999999999" }) {
      success
      message
    }
  }

  mutation {
    verifyOtp(input: { identifier: "9999999999", code: "123456" }) {
      success
      message
      token
    }
  }

Notes:
- This is a starting skeleton. Add validation, rate-limiting, stronger hashing salt, and UI integration as needed.
- For phone-based lookups, create a `mobile` customer attribute or change the lookup logic.
