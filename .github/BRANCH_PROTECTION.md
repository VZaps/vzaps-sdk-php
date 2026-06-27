# Branch protection

Recommended settings for `main` after the first green CI run:

- Require pull requests before merging.
- Require status checks: `validate (8.1)`, `validate (8.2)`, `validate (8.3)`, `validate (8.4)`.
- Require branches to be up to date before merging.
- Require linear history.
- Restrict force pushes and deletions.
