## [1.5.2] - 2025-06-03

### Added
- Admin interface with toggle switches for enabling/disabling individual metrics
- Click-to-copy `/v1/metrics` endpoint URL with visual confirmation
- Plugin metadata section (version, author, GitHub link, Prometheus and Grafana URLs)
- Translation support with `.pot` file (Hungarian default, English-ready)
- New metrics added:
  - Number of plugin and theme updates available
  - Number of users (grouped by role)
  - Number of WordPress options
  - Database query count and total time
  - Remote request count and execution time

### Changed
- Metrics now broken down by post type and user role where applicable
- Updated Prometheus config example (`metrics_path: /v1/metrics`)
- README updated with clear installation and usage instructions

### Fixed
- Redis connection issue with Upstash integration
- Minor YAML and format validation fixes in metric output

