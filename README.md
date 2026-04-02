#WooCommerce Subscription Access Control Demo

TEST

A small portfolio plugin demonstrating subscription-aware access control in WordPress.

#Overview

This plugin shows a simple pattern for protecting content based on membership or subscription state.

It adds a shortcode, [wsacd_protected], that can be used to wrap protected content on posts or pages. When the shortcode is rendered, the plugin checks whether the current user is logged in and whether they qualify for access through one of two paths:

-an active annual subscription
-a designated founder / founding member access flag

If the user qualifies, the protected content is shown. If not, the plugin displays an access message with a link to upgrade or subscribe.

#What This Demonstrates

This repository is intended as a focused example of:

-WordPress plugin structure
-custom access-control logic
-conditional content rendering
-integration-oriented business rules
-maintainable naming and organization
-collision-safe prefixed WordPress code

#Demo Scenario

This demo models a common real-world requirement:

-some users receive access through a paid annual subscription
-some users receive access through a separate founder-member entitlement
-protected content should only render for users who meet one of those conditions

The plugin keeps this intentionally narrow so the access logic is easy to review.

#Example Usage

Wrap protected content like this:

[wsacd_protected]
This content is only visible to qualified users.
[/wsacd_protected]

#Demo Assumptions

This starter example assumes:

-a “Founding Member” access flag exists in Memberium
-an annual subscription product is the primary paid access path
-product IDs, tag IDs, and exact entitlement labels are placeholders for demonstration and should be adapted to the target environment

#Naming Convention

This project uses the iillc_ / IILLC_ prefix for functions, classes, and constants.

This reflects my long-standing production practice of prefixing custom code to reduce collisions in WordPress environments and to make authored code easier to identify during maintenance.

#Scope

This is a portfolio-focused demonstration, not a full production membership platform.

It intentionally does not include:

-full subscription lifecycle management
-billing workflows
-advanced admin configuration
-multi-tier entitlement mapping
-complete Memberium or WooCommerce setup automation

#Installation
1. Install WordPress
2. Install and activate the required dependencies for your test environment
3. Copy this plugin into wp-content/plugins/
4. Activate the plugin
5. Add the shortcode to a test page or post
6. Adjust the demo IDs and access rules for your environment

#Why This Repo Exists

Most of my production work has involved implementing custom business rules inside live WordPress systems rather than building plugins for public release.

This repository extracts one of those patterns into a small, sanitized example that is easier for hiring teams and technical reviewers to evaluate.