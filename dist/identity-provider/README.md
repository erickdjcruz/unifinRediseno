# Identity Provider

- [Versions](#mango-library-releases)
- [Make commands](#make)
- [Docker](#docker)
    - [Jenkins](#jenkins)

# Mango library releases
| Mango version | Library version |
|----|---|
| 11.0.0 | 2.0.0 |
| 10.3.0 | 1.13.0 |
| 10.2.0 | 1.12.0 |
| 10.1.0 | 1.11.0 |
| 10.0.0 | 1.9.0 |
| 9.3.0 | 1.8.0 |
| 9.2.0 | 1.7.0 |
| 9.1.0 | 1.6.2 |
| 9.0.0 | 1.5.1 |
| 8.3.0 | 1.4.2 |
| 8.2.1 | 1.3.2 |
| 8.1.0 | 1.2.1 |
| 8.0.1 | 1.1.7 |
| 7.11.0 | 1.1.0|

To release a new version:

1. Make sure that all changes scheduled for this release are merged into master
1. Update IdentityProvider composer with new version
1. Update "Mango library releases" section in README
1. Create new branch if necessary
1. Create new release on Github
1. Update sugarcrm/identity-provider in composer in LoginService
1. Update and checkout required branch for identity-provider git submodule in Mango
1. Update composer in Mango with new identity-provider release
1. Test LoginService and Mango with the new library (manually and/or with IdentityProvider CI against Mango build containing new identity-provider release)

Login Service uses latest stable library release.

# Make
---
* Install all 3rd-party library dependencies
``` make deps ```

* Run all tests (except Behat E2E tests)
``` make test ```

* Run unit tests
``` make test-unit ```

* Run PSR code-style check test
``` make test-code-standards ```

# Docker
---

#### Jenkins

Docker files like `Dockerfile.php71`, etc. are used mainly for testing in Jenkins Pipeline.
They are not responsible for creating and running Identity Provider as a service and are used for checking the sanity
of IdentityProvider as a library against different versions of PHP.

`Dockerfile.local` is used to simulate Jenkins Pipeline flow in minikube. It allows you to have an Identity Provider
image but with all its files mapped to your local ones so that you can rapidly change them and see
how it affects test runs.

