# WooCommerce Subscription Access Control Demo

A small portfolio plugin demonstrating subscription-aware access control in WordPress.

## Overview

This plugin demonstrates a simple pattern for protecting content based on subscription state. It uses WooCommerce Subscriptions as the primary access check and optionally supports Memberium tag-based access overrides.

It adds a shortcode, [wsacd_protected], that can be used to wrap protected content on posts or pages. When the shortcode is rendered, the plugin checks whether the current user is logged in and whether they qualify for access through one of two paths:

- an active annual subscription
- a designated "VIP" access flag

If the user qualifies, the protected content is shown. If not, the plugin displays an access message with a link to upgrade or subscribe.

## What This Demonstrates

This repository is intended as a focused example of:

- WordPress plugin structure
- custom access-control logic
- conditional content rendering
- integration-oriented business rules
- maintainable naming and organization
- collision-safe prefixed WordPress code

## Demo Scenario

This demo models a common real-world requirement:

- some users receive access through a paid annual subscription
- some users receive access through a separate VIP entitlement
- protected content should only render for users who meet one of those conditions

The plugin keeps this intentionally narrow so the access logic is easy to review.

## Example Usage

Basic usage:

[wsacd_protected]
This content is only visible to qualified users.
[/wsacd_protected]

## Configuration Options

This plugin supports two configuration approaches depending on use case:

### 1. Shortcode Attributes (Inline Control)

You can pass parameters directly into the shortcode to control access behavior per instance:

[wsacd_protected subscription_product_id="123" memberium_tag="VIP" upgrade_url="/upgrade"]

Available attributes:

- `subscription_product_id` — WooCommerce product ID used to validate subscription access
- `memberium_tag` — Memberium tag required for access
- `upgrade_url` — URL shown to users who do not have access

This approach is useful when different pages or sections require different access rules.

---

### 2. Admin Settings (Global Defaults)

The plugin also includes an admin settings page where default values can be configured.

These defaults are used when shortcode attributes are not provided, allowing for simpler usage like:

[wsacd_protected]
Protected content here
[/wsacd_protected]

This approach is useful for consistent site-wide access rules.

## Access Logic

Access is granted if the current user is logged in and meets at least one of the following conditions:

- Has an active subscription matching the configured product ID
- Has the required Memberium tag

If neither condition is met, the user is shown an access restriction message with an optional upgrade link.

## Demo Assumptions

This starter example assumes:

- a “VIP” access flag exists in Memberium
- an annual subscription product is the primary paid access path
- product IDs, tag IDs, and exact entitlement labels are placeholders for demonstration and should be adapted to the target environment
- example output styling assumes Bootstrap-compatible theme styles

## Naming Convention

This project uses the iillc_ / IILLC_ prefix for functions, classes, and constants.

This reflects my long-standing production practice of prefixing custom code to reduce collisions in WordPress environments and to make authored code easier to identify during maintenance.

## Scope

This is a focused demonstration plugin, not a full production membership system.

## Installation
1. Install WordPress
2. Install and activate the required dependencies for your test environment. This plugin requires WooCommerce, Woo Subscriptions, and optionally Memberium.
3. Copy this plugin into wp-content/plugins/
4. Activate the plugin
5. Add the shortcode to a test page or post
6. Adjust the demo IDs and access rules for your environment

## Why This Repo Exists

Most of my production work has involved implementing custom business rules inside live WordPress systems rather than building plugins for public release.

This repository extracts one of those patterns into a small, sanitized example that is easier for hiring teams and technical reviewers to evaluate.

---

## Author

This repository is part of my work in advanced WooCommerce access control and membership systems.

**Troy Whitney**
- 🌐 https://troywhitney.me
- 🏢 https://imageinnovationsllc.com

For consulting or custom WordPress/WooCommerce development, feel free to reach out.

## Repository Status

This repository is a portfolio/demo code sample intended to illustrate a WordPress/WooCommerce access-control pattern.

It is not a production-ready public plugin, and it is not provided as an actively supported installable product.