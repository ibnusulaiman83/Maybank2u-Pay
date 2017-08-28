# Maybank2u Pay

- Integrate The **New Maybank2u Pay** with Popular E-Commerce WooCommerce.
- Please note that this Repository will **not be supported & updated**.

# System Requirements

- **PHP 5.6 & 7.0.** PHP 7.1 and **newer version are not supported due to Maybank SDK** issues.
- **Mod Rewrite Enabled**
- **SSL/TLS Enabled**
- **CloudFlare/Any Reverse Proxy Services Are Not Supported**. If the domain is behind CloudFlare, install it on Subdomain and turn off CloudFlare for the Subdomain or use Page Rule to exclude specific directory from CloudFlare.

# Overview

You **need 2 Installation**. The first one is for "**The System**" to be installed and the second one is for **Integration with WooCommerce**.

For "The System", you **may** need to have 2 separate installation directory/subdomain for **User Acceptance Test** and **Production**.

# 1. System Installation

- Download this repository
- Create database and import **m2u.sql**
- Configure **config.php**
- Copy all files in folder **M2U-Pay-API** to your **public facing directory**
- Done

## Configuration

### Database Information

Below is recommended value:

1. DB Server: **localhost**
2. DB Port: **3306**
3. DB Name: Your Database Name
4. DB Prefix: **m2u_**
5. DB Username: Your Database Username
6. DB Password: Your Database Password

### System Information

1. System URL: https://yourdomain.com/ (**Must end with trailing slash "/"**)
2. Development Mode: **false**
3. Signature: Any Random Character (**Low-Case**)

### Maybank2u Pay Information

1. Payee Code: (**Refer Maybank2u Pay Site**)
2. Mode:
  - **UAT - for User Acceptance Test**
  - **PRODUCTION - for Production Use**
3. Callback ID: Any Random Character (**Low-Case**)

### Bill Settings

1. Bill ID Length: **8**
2. Bill Expiry: **10** (for 10 Minutes)

# 2. WooCommerce Installation

- Download this repository
- Upload the folder **m2upay-for-woocommerce** to **wp-content/plugins** folder
- **Activate** plugin
- Done

## Configuration

### M2upay Payment Gateway

1. Endpoint URL: **Same with System URL**
2. Signature: **Same with Signature**

# Register with Maybank2u Pay

1. Make sure you provide **VALID SSL/TLS Certificate**
2. Callback URL: **SYSTEM URL + callback/ + Callback ID**

3. Example: 
```
https://yourdomain.com/callback/eeffgghhhii
```
4. You may have 2 Installation for both UAT and Production
5. Wait until you get **Payee Code UAT** code and update on **config.php** file
6. Once done, **try to make a payment **using **on** your configured **WooCommerce site**.
7. **Inform** them **if **the payment is **success**
8. **Wait for** the **Production Payee Code**
9. In-case you installed "The System" two times (1 for UAT, 1 for Production), then, you need to update the Production Payee Code and update the Endpoint URL on WooCommerce M2uPay Settings 
9. Otherwise, update the Payee Code on "The System"
9. Enjoy!
