# YBB CMS Menu & Feature Improvements

**Document Version:** 1.0  
**Last Updated:** November 6, 2025  
**Status:** Planning Phase

---

## Table of Contents
1. [Platform Admin Improvements](#platform-admin-improvements)
2. [Tenant Admin Improvements](#tenant-admin-improvements)
3. [Priority Ranking](#priority-ranking)
4. [Implementation Checklist](#implementation-checklist)

---

## Platform Admin Improvements

### Missing/Incomplete Features

#### 1. Analytics & Reporting Dashboard
**Status:** Not Implemented  
**Priority:** High

**Requirements:**
- System-wide metrics dashboard
- Total tenants count (active/suspended/pending)
- Active users across all tenants
- System health indicators
- Tenant growth trends
- User engagement metrics
- Revenue metrics (if applicable)

**Implementation Notes:**
- Create `app/Filament/Widgets/PlatformStatsWidget.php`
- Add to platform dashboard
- Query aggregate data from tenants table

---

#### 2. Audit Logs / Activity Tracking
**Status:** Not Implemented  
**Priority:** Medium

**Requirements:**
- Track all platform admin actions
- Log tenant creation/modification
- Log user assignments
- Log permission changes
- Searchable audit trail
- Filter by admin, action type, date range

**Database Changes Required:**
```sql
CREATE TABLE `audit_logs` (
  `id` char(36) NOT NULL,
  `admin_id` char(36) NOT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` varchar(255) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_admin_id_index` (`admin_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Implementation Notes:**
- Consider using `spatie/laravel-activitylog` package
- Create `AuditLogResource.php` in platform panel
- Add middleware to log actions automatically

---

#### 3. System Settings
**Status:** Not Implemented  
**Priority:** Medium

**Requirements:**
- Centralized system configuration
- Email provider settings (SMTP, API keys)
- Storage configuration (S3, local, etc.)
- API keys management
- System maintenance mode
- Default tenant settings
- Feature flags

**Database Changes Required:**
```sql
CREATE TABLE `system_settings` (
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `is_encrypted` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `system_settings_group_index` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Implementation Notes:**
- Create settings page with tabs for different groups
- Encrypt sensitive values (API keys, passwords)
- Cache settings for performance

---

### Organizational Improvements

#### 5. Rename & Clarify Admin Resources
**Status:** Needs Refactoring  
**Priority:** High

**Changes Required:**
- Rename `AdminResource` to `PlatformAdminResource` for clarity
- Update navigation labels:
  - "Admins" → "Platform Admins"
  - "Users" → "Tenant Users"
- Add descriptions/tooltips to clarify differences

**Files to Modify:**
- `app/Filament/Resources/AdminResource.php` → rename file and class
- Update namespace references
- Update navigation labels

---

#### 6. Enhanced Tenant Overview
**Status:** Partially Implemented  
**Priority:** Medium

**Current State:** `ViewTenant` page exists with tabs

**Improvements Needed:**
- Add recent activity timeline
- Show resource usage metrics:
  - Storage used
  - Bandwidth consumed
  - API calls made
- Quick actions in header:
  - Suspend/Resume tenant
  - Reset admin credentials
  - Export tenant data
  - Clone tenant settings

**Files to Modify:**
- `app/Filament/Resources/TenantResource/Pages/ViewTenant.php`

---

## Tenant Admin Improvements

### Missing Critical Features

#### 1. Dashboard Widget
**Status:** Not Implemented  
**Priority:** High

**Requirements:**
- Key metrics overview:
  - Total posts (by status)
  - Published content count
  - Pending reviews count
  - Traffic stats (last 30 days)
  - Ad performance summary
  - Newsletter subscribers count
- Quick action buttons:
  - Create New Post
  - Create New Program
  - Create New Job
  - View Analytics
- Recent activity feed

**Implementation Steps:**
1. Create `app/Filament/User/Widgets/TenantDashboardWidget.php`
2. Create `app/Filament/User/Widgets/QuickStatsWidget.php`
3. Create `app/Filament/User/Widgets/RecentActivityWidget.php`
4. Register widgets in User panel dashboard

---

#### 2. Programs & Jobs as Separate Resources
**Status:** Currently Embedded in PostResource  
**Priority:** High

**Current Issue:** Programs and Jobs are post types with conditional fields in `PostResource`

**Solution:** Create Virtual Resources (Option 2 - SELECTED)
- Create `ProgramResource.php` - virtual resource scoped to `kind='program'`
- Create `JobResource.php` - virtual resource scoped to `kind='job'`
- Update `PostResource.php` to only handle pages, news, guides (`kind IN ['page','news','guide']`)
- All three resources use the same `Post` model but with scoped queries

**Benefits:**
- Better UX - specific table columns for each type
- Advanced filtering for programs (by funding type, deadline, country)
- Advanced filtering for jobs (by employment type, workplace, salary range)
- Cleaner form layouts without conditional visibility
- Separate permission controls
- Matches database structure (posts with extended pt_program/pt_job tables)

**Database:** Already supports this (tables: `posts`, `pt_program`, `pt_job`)

**Implementation Details:**
```php
// ProgramResource.php - Virtual resource for programs
class ProgramResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 2;
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('kind', 'program')
            ->with('program'); // eager load pt_program
    }
}

// JobResource.php - Virtual resource for jobs  
class JobResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 3;
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('kind', 'job')
            ->with('job'); // eager load pt_job
    }
}

// PostResource.php - Updated to exclude programs/jobs
class PostResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('kind', ['page', 'news', 'guide']);
    }
}
```

**Files to Create:**
```
app/Filament/User/Resources/ProgramResource.php
app/Filament/User/Resources/ProgramResource/Pages/ListPrograms.php
app/Filament/User/Resources/ProgramResource/Pages/CreateProgram.php
app/Filament/User/Resources/ProgramResource/Pages/EditProgram.php

app/Filament/User/Resources/JobResource.php
app/Filament/User/Resources/JobResource/Pages/ListJobs.php
app/Filament/User/Resources/JobResource/Pages/CreateJob.php
app/Filament/User/Resources/JobResource/Pages/EditJob.php
```

**Files to Modify:**
```
app/Filament/User/Resources/PostResource.php (update query scope)
```

**Navigation Structure:**
```
📰 Content
   - Posts (pages, news, guides only)
   - Programs (scholarship, opportunity, internship)
   - Jobs
   - Categories
   - Tags
   - Media Library
```

---

#### 3. Ad Management Split
**Status:** Needs Implementation  
**Priority:** Medium

**Current State:** Likely single Ad resource (needs verification)

**Proposed Structure:**
- **AdResource** - Create and manage ad campaigns
- **AdAnalyticsResource** - View-only dashboard for performance

**AdAnalyticsResource Features:**
- Date range filtering
- Charts: impressions, clicks, CTR over time
- Top performing ads
- Performance by placement
- Export reports

**Files to Create:**
```
app/Filament/User/Resources/AdResource.php (may exist)
app/Filament/User/Resources/AdAnalyticsResource.php (new)
```

---

#### 4. Newsletter Improvements
**Status:** Basic Implementation  
**Priority:** Medium

**Current State:** `newsletter_subscriptions` table exists

**Required Resources:**
1. **SubscriberResource** - Manage subscriber list
   - CRUD operations
   - Bulk import CSV
   - Bulk export
   - Tag management
   - Segment creation

2. **SubscriptionFormResource** - Generate embeddable forms
   - Form builder
   - Generate embed code
   - Customizable fields
   - Success message customization

**Files to Create:**
```
app/Filament/User/Resources/SubscriberResource.php
app/Filament/User/Resources/SubscriberResource/Pages/
app/Filament/User/Resources/SubscriptionFormResource.php
```

**Database Changes:**
```sql
-- Add segments support
CREATE TABLE `subscriber_segments` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `criteria` json NOT NULL,
  `subscribers_count` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriber_segments_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

#### 5. Email Campaign Builder
**Status:** Basic Schema Exists  
**Priority:** Medium

**Current State:** `email_campaigns` table exists but implementation likely basic

**Enhancements Needed:**
- Drag-and-drop email builder (use library like `unlayer`, `grapesjs`)
- Template library (save and reuse templates)
- Merge tags for personalization
- Preview on different devices
- Test email functionality
- A/B testing capability
- Detailed analytics per campaign:
  - Opens over time
  - Click heatmap
  - Link performance
  - Unsubscribe reasons

**Files to Enhance:**
```
app/Filament/User/Resources/EmailCampaignResource.php (may exist)
app/Filament/User/Resources/EmailTemplateResource.php (new)
```

---

#### 6. Comment/Review Management
**Status:** Not Implemented  
**Priority:** Medium

**Requirements:**
- Comments on posts
- Moderation queue
- Approve/reject comments
- Spam detection
- Reply to comments
- User reputation system

**Database Changes Required:**
```sql
CREATE TABLE `comments` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `parent_id` char(36) DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected','spam') DEFAULT 'pending',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `likes_count` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_tenant_id_post_id_index` (`tenant_id`,`post_id`),
  KEY `comments_status_index` (`status`),
  KEY `comments_parent_id_index` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Files to Create:**
```
app/Filament/User/Resources/CommentResource.php
app/Models/Comment.php
```

---

#### 7. User Roles & Permissions (Tenant-Level)
**Status:** Needs Implementation  
**Priority:** High

**Current Issue:** Only platform admins can see `RoleResource` and `PermissionResource`

**Solution:**
- Duplicate role/permission resources for User panel
- Scope to tenant's team members only
- Tenant admins can:
  - Assign roles to their team
  - View permission matrix
  - Cannot create new roles (use predefined ones)

**Files to Create:**
```
app/Filament/User/Resources/TeamMemberResource.php
app/Filament/User/Resources/TeamRoleResource.php (view-only)
```

---

### Navigation Structure Improvements

#### 8. Reorganize Menu Groups
**Status:** Needs Refactoring  
**Priority:** High

**Current Structure:**
```
Content
  - Posts
  - Terms
  - Media
```

**Proposed Structure:**
```
📰 Content
   - Posts (pages, news, guides)
   - Terms (Categories, Tags, Locations, Skills, Industries)
   - Media Library
   - Comments (new)

💼 Opportunities
   - Programs (Scholarships, Internships, etc.)
   - Jobs
   
📢 Marketing
   - Ads
   - Ad Analytics
   - Email Campaigns
   - Newsletter Subscribers
   - SEO Landing Pages
   
🔧 Tools
   - Redirects
   - Analytics
   - Bulk Import/Export
   
⚙️ Settings
   - Tenant Profile
   - Team Members
   - Domains
   - Integrations (GA, AdSense, etc.)
   - Legal & Privacy
```

**Implementation:**
- Update `navigationGroup` in all User panel resources
- Add `navigationSort` values
- Add `navigationIcon` for better visual hierarchy

---

#### 9. Quick Actions
**Status:** Not Implemented  
**Priority:** Medium

**Requirements:**
- Floating action button (FAB) in bottom-right corner
- Command palette (Cmd+K or Ctrl+K)
- Global search across all content
- Quick create shortcuts:
  - New Post
  - New Program
  - New Job
  - New Campaign

**Implementation:**
- Use Filament's global search feature
- Add custom keyboard shortcuts
- Consider `wire-elements/modal` for quick create forms

---

#### 10. Bulk Operations
**Status:** Partially Implemented  
**Priority:** Medium

**Current State:** Limited bulk actions in tables

**Enhanced Bulk Actions:**
- Posts:
  - Bulk publish/unpublish
  - Bulk category assignment
  - Bulk tag assignment
  - Bulk schedule
  - Bulk export (CSV, JSON)
  - Bulk delete with safeguards
- Programs/Jobs:
  - Bulk status change
  - Bulk deadline update
  - Bulk export
- Media:
  - Bulk delete
  - Bulk move to collection
  - Bulk download as ZIP

**Files to Modify:**
- Update `bulkActions()` in all Resource files

---

### Content Management Enhancements

#### 11. Content Scheduler/Calendar View
**Status:** Not Implemented  
**Priority:** Medium

**Requirements:**
- Calendar view of scheduled posts
- Drag-and-drop to reschedule
- Color-coded by post type
- Filter by status, type, author
- Quick preview on hover
- Export calendar as ICS

**Database:** Already supports with `scheduled_at` field

**Implementation:**
- Use FullCalendar.js
- Create custom Filament page
- Add to Content navigation group

**Files to Create:**
```
app/Filament/User/Pages/ContentCalendar.php
resources/views/filament/user/pages/content-calendar.blade.php
```

---

#### 12. Media Organization
**Status:** Basic Implementation  
**Priority:** Medium

**Current State:** `collection_name` field exists but likely not fully utilized

**Enhancements:**
- Folder/album tree view
- Drag-and-drop file organization
- Bulk upload with progress indicator
- Media usage tracking (which posts use this media)
- Unused media detection
- Image editing (crop, resize, filters)

**Files to Enhance:**
```
app/Filament/User/Resources/MediaResource.php
```

**Consider Package:** `spatie/laravel-medialibrary` (may already be in use)

---

#### 13. SEO Tools Dashboard
**Status:** Basic Implementation  
**Priority:** Medium

**Current State:** `SeoLandingResource` and `RedirectResource` exist

**Additional SEO Tools:**
1. **SEO Audit** - Scan all posts for issues:
   - Missing meta descriptions
   - Duplicate meta titles
   - Broken images
   - Missing alt text
   - Keyword density

2. **Broken Link Checker** - Scan content for 404s

3. **Sitemap Generator** - Auto-generate XML sitemap

4. **Meta Tag Validator** - Preview how posts appear in search/social

5. **Keyword Tracker** - Track keyword rankings (external API integration)

**Files to Create:**
```
app/Filament/User/Pages/SeoAudit.php
app/Filament/User/Pages/BrokenLinks.php
app/Filament/User/Pages/SitemapGenerator.php
```

---

#### 14. Content Workflow
**Status:** Database Schema Supports, UI Incomplete  
**Priority:** High

**Current State:** Post status supports: draft → review → scheduled → published

**Required Workflow UI:**
- "Submit for Review" button (changes status to 'review')
- Review queue page for Editors
- Approve/Reject actions with comments
- Revision history (track all changes)
- Inline comments/feedback on drafts
- Email notifications on status changes

**Database Changes:**
```sql
CREATE TABLE `post_revisions` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `content_snapshot` json NOT NULL,
  `changes_made` text DEFAULT NULL,
  `version_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_revisions_post_id_index` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `post_comments` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `comment` text NOT NULL,
  `is_resolved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_comments_post_id_index` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Files to Create:**
```
app/Filament/User/Pages/ReviewQueue.php
app/Filament/User/Resources/PostResource/RelationManagers/RevisionsRelationManager.php
```

---

### Analytics Improvements

#### 15. Content Analytics
**Status:** Database Table Exists, Dashboard Missing  
**Priority:** High

**Current State:** `analytics_events` table exists

**Required Analytics Dashboard:**
- **Per-Post Analytics:**
  - Page views
  - Unique visitors
  - Average time on page
  - Bounce rate
  - Traffic sources (direct, search, social, referral)
  - Geographic distribution
  - Device breakdown (mobile, desktop, tablet)
  - Social shares count
  
- **Conversion Tracking:**
  - Apply button clicks (for programs/jobs)
  - Form submissions
  - Email signups
  - Goal completions

- **Top Content:**
  - Most viewed posts
  - Trending content
  - Best performing authors

**Files to Create:**
```
app/Filament/User/Pages/Analytics.php
app/Filament/User/Widgets/ContentPerformanceWidget.php
app/Filament/User/Widgets/TrafficSourcesWidget.php
app/Filament/User/Widgets/TopContentWidget.php
```

**Integration:** Consider integrating with Google Analytics API for enhanced data

---

#### 16. Ad Performance Dashboard
**Status:** Database Tables Exist, Dashboard Missing  
**Priority:** Medium

**Current State:** `ad_impressions` and `ad_clicks` tables exist

**Required Dashboard Features:**
- **Overview Metrics:**
  - Total impressions
  - Total clicks
  - Average CTR
  - Revenue (if applicable)

- **Charts:**
  - Impressions/Clicks over time (line chart)
  - CTR trends
  - Performance by placement (bar chart)
  - Performance by device type

- **Ad Comparison:**
  - Side-by-side comparison
  - Best/worst performing ads

- **Export Reports:**
  - PDF reports
  - CSV exports
  - Scheduled email reports

**Files to Create:**
```
app/Filament/User/Pages/AdAnalyticsDashboard.php
app/Filament/User/Widgets/AdPerformanceWidget.php
app/Filament/User/Widgets/AdTrendsWidget.php
```

---

### User Experience Enhancements

#### 17. Multi-Tenant Switcher
**Status:** Not Implemented  
**Priority:** High

**Requirements:**
- For users who belong to multiple tenants
- Dropdown in header/navbar showing current tenant
- Quick switch between tenants
- Show tenant logo/name
- Remember last selected tenant

**Implementation:**
- Add middleware to track current tenant in session
- Create custom Livewire component for switcher
- Place in User panel navigation/header

**Files to Create:**
```
app/Http/Middleware/SetCurrentTenant.php
app/Livewire/TenantSwitcher.php
resources/views/livewire/tenant-switcher.blade.php
```

**User Flow:**
1. User logs in
2. If user belongs to multiple tenants, show tenant selection
3. Store selected tenant in session
4. Scope all queries to selected tenant
5. Allow switching via dropdown

---

#### 18. Notifications Center
**Status:** Not Implemented  
**Priority:** Medium

**Requirements:**
- Bell icon in header with unread count badge
- Notification dropdown
- Mark as read/unread
- Clear all notifications
- Notification types:
  - Post pending review (for Editors)
  - Comment awaiting moderation
  - Campaign sent successfully
  - Low ad inventory
  - User invited to tenant
  - New domain verified

**Database Changes:**
```sql
-- Use Laravel's built-in notifications table
php artisan notifications:table
php artisan migrate
```

**Implementation:**
- Use Laravel's notification system
- Create notification classes for each type
- Send via database channel
- Create Filament notification panel widget

**Files to Create:**
```
app/Notifications/PostPendingReviewNotification.php
app/Notifications/CommentAwaitingModerationNotification.php
app/Notifications/CampaignSentNotification.php
(etc.)
```

---

#### 19. Help & Documentation
**Status:** Not Implemented  
**Priority:** Low

**Requirements:**
- Contextual help tooltips on forms
- Help icon in navigation
- Link to external documentation
- Video tutorials embedded
- Searchable knowledge base
- In-app chat support (optional)

**Implementation Options:**
1. **Simple:** Add help text to form fields using `helperText()`
2. **Moderate:** Create Help resource with articles
3. **Advanced:** Integrate with helpdesk software (Intercom, Zendesk)

**Files to Create (if building in-app):**
```
app/Filament/User/Resources/HelpArticleResource.php
app/Filament/User/Pages/HelpCenter.php
```

---

## Priority Ranking

### **High Priority** (Implement First)

1. ✅ **Split Programs & Jobs into separate resources**
   - Better UX, cleaner code
   - Files: `ProgramResource.php`, `JobResource.php`
   - Estimated Time: 4-6 hours

2. ✅ **Create Dashboard widgets for both panels**
   - Essential for overview
   - Platform: System stats, tenant growth
   - Tenant: Content stats, quick actions
   - Estimated Time: 6-8 hours

3. ✅ **Add proper role management for tenant admins**
   - Critical for team management
   - Files: `TeamMemberResource.php`
   - Estimated Time: 3-4 hours

4. ✅ **Improve navigation grouping**
   - Better information architecture
   - Update all Resource files
   - Estimated Time: 2-3 hours

5. ✅ **Add multi-tenant switcher**
   - Essential for users in multiple tenants
   - Files: Middleware, Livewire component
   - Estimated Time: 4-5 hours

6. ✅ **Content Workflow (Review Queue)**
   - Critical for editorial process
   - Database migrations + UI
   - Estimated Time: 8-10 hours

7. ✅ **Analytics Dashboard (Content Performance)**
   - Essential for data-driven decisions
   - Files: Analytics pages, widgets
   - Estimated Time: 8-12 hours

### **Medium Priority** (Next Phase)

8. ⏳ **Ad analytics dashboard**
   - Important for ad-supported sites
   - Estimated Time: 4-6 hours

9. ⏳ **Email campaign builder**
   - Enhanced email marketing
   - Estimated Time: 12-16 hours (if building from scratch)

10. ⏳ **Content calendar view**
    - Visual content planning
    - Estimated Time: 6-8 hours

11. ⏳ **SEO tools dashboard**
    - Important for organic traffic
    - Estimated Time: 8-10 hours

12. ⏳ **Audit logs for platform admins**
    - Security and compliance
    - Estimated Time: 4-6 hours

13. ⏳ **Media organization improvements**
    - Better asset management
    - Estimated Time: 6-8 hours

14. ⏳ **Newsletter enhancements**
    - Subscriber management
    - Estimated Time: 6-8 hours

### **Low Priority** (Nice to Have)

15. 📋 **Comment management**
    - Community engagement
    - Estimated Time: 8-10 hours

16. 📋 **Notification center**
    - Better user engagement
    - Estimated Time: 6-8 hours

17. 📋 **A/B testing for campaigns**
    - Advanced marketing feature
    - Estimated Time: 10-12 hours

18. 📋 **Advanced bulk operations**
    - Power user features
    - Estimated Time: 4-6 hours

19. 📋 **Help & Documentation system**
    - User support
    - Estimated Time: 8-12 hours

---

## Implementation Checklist

### Phase 1: Foundation (High Priority Items)

#### Week 1-2
- [ ] Create `ProgramResource.php` with full CRUD
- [ ] Create `JobResource.php` with full CRUD
- [ ] Update `PostResource.php` to only handle pages, news, guides
- [ ] Test program/job creation and editing
- [ ] Update navigation groups for all resources
- [ ] Set proper `navigationSort` values

#### Week 3
- [ ] Create platform dashboard widgets
  - [ ] `TenantStatsWidget.php`
  - [ ] `SystemHealthWidget.php`
- [ ] Create tenant dashboard widgets
  - [ ] `ContentStatsWidget.php`
  - [ ] `QuickActionsWidget.php`
  - [ ] `RecentActivityWidget.php`

#### Week 4
- [ ] Implement multi-tenant switcher
  - [ ] Create middleware
  - [ ] Create Livewire component
  - [ ] Update navigation to show switcher
  - [ ] Test switching between tenants
- [ ] Create `TeamMemberResource.php`
- [ ] Add role assignment functionality

### Phase 2: Content & Analytics (High Priority Continued)

#### Week 5-6
- [ ] Create content workflow system
  - [ ] Migration: `post_revisions` table
  - [ ] Migration: `post_comments` table
  - [ ] Create `ReviewQueue.php` page
  - [ ] Add "Submit for Review" action
  - [ ] Add approval/rejection workflow
  - [ ] Set up email notifications

#### Week 7-8
- [ ] Build analytics dashboard
  - [ ] Create `Analytics.php` page
  - [ ] Create analytics widgets
  - [ ] Integrate with `analytics_events` table
  - [ ] Add charts and visualizations
  - [ ] Test data accuracy

### Phase 3: Marketing & SEO (Medium Priority)

#### Week 9-10
- [ ] Enhance ad management
  - [ ] Create `AdAnalyticsResource.php`
  - [ ] Build performance dashboard
  - [ ] Add charts and reports
- [ ] Build email campaign enhancements
  - [ ] Research email builder libraries
  - [ ] Integrate builder
  - [ ] Create template system
  - [ ] Add A/B testing (if time allows)

#### Week 11-12
- [ ] Create content calendar
  - [ ] Build calendar view page
  - [ ] Implement drag-and-drop
  - [ ] Add filtering options
- [ ] Build SEO tools
  - [ ] Create SEO audit page
  - [ ] Implement broken link checker
  - [ ] Add meta tag validator

### Phase 4: Polish & Advanced Features (Medium/Low Priority)

#### Week 13-14
- [ ] Implement audit logs
  - [ ] Create migration
  - [ ] Set up activity logging
  - [ ] Create `AuditLogResource.php`
- [ ] Enhance media library
  - [ ] Add folder organization
  - [ ] Implement usage tracking
  - [ ] Add bulk operations

#### Week 15-16
- [ ] Newsletter enhancements
  - [ ] Create `SubscriberResource.php`
  - [ ] Add import/export functionality
  - [ ] Create segment management
- [ ] Comments system (if needed)
  - [ ] Create migration
  - [ ] Create `CommentResource.php`
  - [ ] Add moderation workflow

---

## Testing Strategy

### Unit Tests
- Model relationships
- Business logic
- Permission checks

### Feature Tests
- Resource CRUD operations
- Workflow transitions
- Bulk actions
- Multi-tenant scoping

### Browser Tests (Dusk)
- Dashboard interactions
- Form submissions
- Tenant switching
- Content calendar

---

## Performance Considerations

### Database Optimization
- [ ] Add indexes for frequently queried columns
- [ ] Implement database query caching
- [ ] Use eager loading to prevent N+1 queries
- [ ] Consider read replicas for analytics

### Frontend Optimization
- [ ] Lazy load dashboard widgets
- [ ] Implement infinite scroll for large tables
- [ ] Cache computed values
- [ ] Optimize image loading

### Caching Strategy
- [ ] Cache system settings
- [ ] Cache tenant configurations
- [ ] Cache analytics aggregates
- [ ] Implement Redis for session storage

---

## Security Considerations

### Platform Admin Panel
- [ ] Implement 2FA for platform admins
- [ ] IP whitelist for platform access
- [ ] Rate limiting on sensitive actions
- [ ] Audit all admin actions

### Tenant Admin Panel
- [ ] Proper tenant isolation (query scoping)
- [ ] Permission-based access control
- [ ] CSRF protection
- [ ] XSS prevention in rich text content

### Data Protection
- [ ] Encrypt sensitive configuration
- [ ] Regular database backups
- [ ] Data retention policies
- [ ] GDPR compliance features

---

## Documentation Tasks

- [ ] Update README with new features
- [ ] Create admin user guide
- [ ] Create tenant user guide
- [ ] Document API endpoints (if applicable)
- [ ] Create video tutorials
- [ ] Document deployment process

---

## Notes

- This is a living document - update as requirements change
- Prioritize based on user feedback and business needs
- Consider user testing before major releases
- Keep backward compatibility in mind
- Document breaking changes

---

**Last Updated:** November 6, 2025  
**Next Review:** When starting Phase 2 implementation
