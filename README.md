# SVP Fundraise Up Wordpress plugin

This plugin allows for two accounts and their campaigns to be added on a donate page, with a mechanism to switch between them

## Installation

First clone this repo with `git clone`

1. WIthin the Wordpress admin panel select `Plugins | Add Plugin`
2. Now, top of the screen, click the `Upload Plugin` button
3. Now click `Choose File` and navigate to wherever you cloned the repo and select the `svp-fru.zip` File
4. Back at the `Add Plugins` screen, click `Install Now`
5. Finally, when the plugin is installed click the `Activate Plugin` button

## Directions for use

### Fundraise Up

To install Fundraise Up for account switching you need a Subaccount setup for the Giftaid (GB) option and some details from your current main account campaign

1. Create a subaccount (`Settings | Subaccounts`) and within that subaccount create a campaign, or better still, clone a campaign from the main account

2. Now on the Subaccounts screen you can find the ID of your subaccount. You'll also need the ID of the Campaign (`Campaigns`)

3. Now switch back to the main account and again find the ID of both the account and the active campaign. The account ID is a little trickier to find on the main account, you can find it by selecting your active campaign on the Campaigns screen. The ID is top left, directly under the campaign name

### Wordpress

1. Add the `[fru_install]` shortcode to the site header template

```
[fru_install gb_widget="AAAAAAAA" intl_widget="BBBBBBBB"]
```

1. Optionally add the `[fru_link]` shortcode wherever you'd like the FRU link element to appear

```
[fru_link gb_href="#CCCCCCCC" intl_href="#DDDDDDDD"]
```

1. Optionally add the `[fru_switch]` shortcode wherever you'd like the switch function to appear

```
[fru_switch label="Custom text"]
```

## New version release

1. Bump the version number in the .php file   __<---always do this part!__

2. First tag the commit as a release, we force as that tag already exists

```
git tag -f release
```

1. Now push to the remote repo

```
 git push origin main
```
