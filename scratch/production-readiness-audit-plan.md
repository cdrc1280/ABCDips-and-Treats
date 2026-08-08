# ABCDips Production Readiness Implementation Plan

## Critical Fixes

1. **Lock down dev/admin UI routes**
   - Remove or gate `/dev/components`, `/pos`, `/ai-advisor` behind admin auth in router and/or feature flag.
   - Ensure AI widget is not rendered unless user is `isAdmin`.

2. **OTP security**
   - Remove OTP display or sandbox hints from frontend.
   - Keep only hashed OTP storage in cache and never expose the code in responses.
   - Add rate limiting / throttle if not present.

3. **Remove test/demo seeders and routes**
   - Replace `admin@abcdips.test` with environment-driven admin account or document it as dev-only.
   - Remove or isolate `.test` sample HR records from payroll seeder.
   - Remove any public test-only routes from `routes/web.php` and `routes/api.php`.

4. **Auth/session handling**
   - Document current token vs cookie strategy.
   - Avoid additional insecure localStorage token usage where possible.
   - Ensure bearer token and Sanctum cookie behavior are consistent.

5. **Blog/contact route cleanup**
   - Move blog routes into controller methods.
   - Avoid inline closures with silent catch blocks.
   - Return consistent API response shapes.

## High Priority Fixes

6. **Admin authorization hardening**
   - Confirm `User::canAccessPanel` is explicit role-based only.

7. **Analytics correctness**
   - Fix `aboutStats()` to use `hasRole('customer')` or correct role field.

8. **Validation standardization**
   - Introduce request classes for key controllers where absent.

9. **Dev-only page protection**
   - Ensure `/dev` pages are not available in production bundles unless admin.

10. **Remove placeholder blog content**
   - Use real model loading only.

11. **Error response consistency**
   - Align API response shapes for success/errors across controllers.

## Medium/Low Fixes

12. Logging configuration
13. Cache/queue production config docs
14. Report route protection (already protected)
15. Sanctum auth consistency doc
16. Cart merge reliability
17. Remove committed vendor assets if not needed
18. Improve frontend error UX
19. Accessibility audit
20. AI prompt validation and rate limiting
21. Dependency pinning review
22. Composer stability settings
23. Frontend validation composables
24. Remove test placeholder hints
25. Add production env docs

---

## Implementation Notes

- Focus on the most important production fixes first.
- Any seeders or dev sample data should remain only in local/dev environment flows.
- Add comments or README notes where env-specific behavior is used.

