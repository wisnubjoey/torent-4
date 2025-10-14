<!--
Sync Impact Report
Version: N/A → 1.0.0
Modified Principles:
- PRINCIPLE_1_NAME placeholder → Code Quality Discipline
- PRINCIPLE_2_NAME placeholder → Test Coverage for Every Feature
- PRINCIPLE_3_NAME placeholder → Accessible, Responsive UX
- PRINCIPLE_4_NAME placeholder → Lean Performance Delivery
- PRINCIPLE_5_NAME placeholder → Proactive Security Hygiene
Added Sections:
- Operational Standards
- Development Workflow & Quality Gates
Removed Sections: None
Templates requiring updates:
- .specify/templates/plan-template.md ✅ updated
- .specify/templates/spec-template.md ✅ updated
- .specify/templates/tasks-template.md ✅ updated
- Command templates (not present): no action
Follow-up TODOs: None
-->
# Torent 4 Constitution

## Core Principles

### I. Code Quality Discipline
- All PHP code MUST pass `vendor/bin/pint`; JS/TS MUST pass `npm run format:check` and `npm run lint` before review.
- Duplicate logic MUST be refactored into Laravel services, traits, or shared React hooks/components to keep
  features modular.
- Modules MUST declare clear contracts and stay small; extract cross-cutting concerns into dedicated classes
  or composables.

Rationale: Enforcing style, reuse, and modularity keeps the codebase predictable and enables the team to scale
delivery without accumulating brittle hotspots.

### II. Test Coverage for Every Feature
- Every backend change MUST ship with Pest unit coverage plus at least one integration test hitting HTTP or queue
  entry points.
- Every frontend change MUST include component or interaction tests that exercise the Inertia/Vite bundle end to end.
- No pull request merges until automated test suites run green in CI and locally.

Rationale: Consistent unit and integration coverage makes regressions cheap to catch and safeguards future
scaling by locking in expected behavior as the system grows.

### III. Accessible, Responsive UX
- Interfaces MUST be responsive across Tailwind breakpoints and align with shared design tokens.
- All user flows MUST satisfy WCAG 2.1 AA: keyboard navigation, focus states, semantic HTML, aria labels, and
  color contrast.
- Reusable UI primitives live in `resources/js/components`; pages compose them to keep presentation consistent.

Rationale: Accessibility and responsive consistency sustain maintainability by reducing bespoke tweaks and grow
adoption across devices as the product scales.

### IV. Lean Performance Delivery
- Optimize and compress media assets via Vite pipelines; images MUST ship in modern formats with dimension hints.
- Defer non-critical bundles using code splitting and lazy loading (dynamic imports or React.lazy) for Inertia pages.
- Monitor bundle size and keep dependencies minimal; remove unused packages during feature work.

Rationale: Tight performance constraints prevent runaway bundle growth, keeping the app fast for new users and
manageable as feature count scales.

### V. Proactive Security Hygiene
- All external input MUST pass Laravel request validation and explicit sanitization before persistence or rendering.
- Escape user content in React components and guard against XSS/CSRF by relying on framework helpers and HTTP
  middleware.
- Secrets and credentials stay in `.env`; never commit sensitive data or log personally identifiable information.

Rationale: Systematic validation and sanitization preserve trust, limit breach blast radius, and keep the
platform resilient as traffic and integrations increase.

## Operational Standards

- Environments bootstrap via `composer install`, `npm install`, `.env` provisioning, and `php artisan migrate
  --force`; document new setup steps in `docs/` or the relevant feature spec.
- Shared tooling (Pint, Prettier, ESLint, Pest) MUST stay pinned via Composer or npm scripts; update lockfiles
  with each change.
- Deployment artifacts come from `npm run build` or `npm run build:ssr`; publish release notes covering
  performance or security considerations introduced.

## Development Workflow & Quality Gates

- Feature work begins with a plan/spec referencing this constitution; each plan MUST list checkpoints for code
  quality, testing, UX, performance, and security.
- Pull requests MUST include: summary, linked spec/task, screenshots for UI, and explicit test evidence
  (commands + outcomes).
- Reviews block merges until principles are satisfied; violations require remediation tasks tracked in `tasks.md`.
- Post-merge, monitor logs and metrics to confirm no regressions in accessibility, performance, or security.

## Governance

- This constitution supersedes prior process docs; conflicts resolve in favor of the latest version herein.
- Amendments require consensus from the maintainers, documented rationale, and simultaneous updates to
  impacted templates/docs.
- Versioning follows SemVer: major for principle removals or incompatible shifts, minor for new principles or
  sections, patch for clarifications. Record the next version in the footer.
- Compliance reviews occur each release cycle; findings feed into the plan/spec templates and task backlogs.

**Version**: 1.0.0 | **Ratified**: 2025-10-14 | **Last Amended**: 2025-10-14
