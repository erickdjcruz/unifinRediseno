# Maple Syrup

A design token implementation for Sugar products.

## Getting started

Clone your fork of this repo and start building away. If you need to build the tokens locally, use the following command:

```shell
$  yarn install
$  yarn run build
```

### Feature branches

If you're working on a feature branch locally, it's easiest to head into the project where maple-syrup is a submodule and
add your fork as a new remote. From there, you can fetch the remote and checkout your branch there. 

### Pull requests 

When you push your branch to create a PR, please only include the updated tokens files, and not the `build` directory.
There is a build step to generate tokens. 

#### Workflows (GitHub Actions)

When your PR is ready to be merged, there is an action that will run on merge to main that triggers the design tokens 
to be built post-merge. This adds an extra commit to the repo.

By default, this build step on runs on merges to the main branch. If you'd like run this workflow on one of the main repo's
branches, you can do so using the GitHub CLI:

```shell
$  gh workflow run 'Automatic build' --ref=<branch-name>
```

_Note_: If the `ref` parameter is not added this will build and commit to the `main` branch.

You can then see your workflow running here:

```shell
$  gh workflow list --workflow=main.yml
```

## Structure

All the design tokens are nested under the `tokens/` directory, and are grouped by token type. For example, 
`tokens/color/` houses all the color variables used in the Sugar palette. 

## How does this repo work?

Using namespaced JSON and Amazon's [Style Dictionary](https://github.com/amzn/style-dictionary) library, we're able to 
build an easy to write and maintain platform with the ability to output to any desired format. This allows the tool to 
stay agnostic and flexible, while each product-level implementation to be specific.

### Transforms

_Note_: This section will be expanded over time. 

If you're interested in having Maple Syrup output to a new format, please add a new entry under the `platforms` property
in `config.js`.

### About

This project was pulled from Sprint Week '22. The motivation was, and is, to make atomic level details of the style 
toolchain consistent and reusable for every product at Sugar.

