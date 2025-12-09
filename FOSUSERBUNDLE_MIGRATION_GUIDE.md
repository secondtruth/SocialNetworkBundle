# FOSUserBundle Migration Guide

## Overview

FOSUserBundle is deprecated and no longer maintained. This guide outlines the steps needed to migrate away from FOSUserBundle to a custom authentication system compatible with Symfony 7.

## Current FOSUserBundle Dependencies

The bundle currently relies on FOSUserBundle for:

1. **User Management**
   - User entity base class
   - User registration
   - Email confirmation
   - Password reset/resetting
   - User profile management

2. **Authentication**
   - Login/logout functionality
   - Security integration
   - Remember me functionality

3. **Controllers**
   - `RegistrationController` (extends FOSUserBundle)
   - `ProfileController` (extends FOSUserBundle)
   - `ResettingController` (extends FOSUserBundle)

4. **Forms**
   - `RegistrationFormType` (extends FOSUserBundle)
   - `ProfileFormType` (extends FOSUserBundle)
   - `ResettingFormType` (extends FOSUserBundle)

5. **Services & Mailers**
   - `fos_user.user_manager`
   - `fos_user.mailer`
   - Email templates for registration, confirmation, and password reset

## Migration Strategy

### Phase 1: Create Custom User Management System

#### 1.1 Update User Entity
The `User` entity already exists at `src/Bundle/SocialNetworkBundle/Entity/User.php`. It needs to:
- Remove FOSUserBundle base class inheritance
- Implement Symfony's `UserInterface` and `PasswordAuthenticatedUserInterface`
- Add all necessary fields from FOSUserBundle base class

```php
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Implement required methods:
    // - getUserIdentifier(): string
    // - getRoles(): array
    // - getPassword(): ?string
    // - eraseCredentials(): void
    // - getSalt(): ?string (can return null in Symfony 6+)
}
```

#### 1.2 Create User Repository
Update `src/Bundle/SocialNetworkBundle/Repository/UserRepository.php`:
- Implement `PasswordUpgraderInterface` for automatic password rehashing
- Add methods for finding users by email, username, confirmation token

#### 1.3 Create User Manager Service
Replace `fos_user.user_manager` with a custom service:

```php
namespace Kiboko\Bundle\SocialNetworkBundle\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function createUser(): User { }
    public function updateUser(User $user): void { }
    public function deleteUser(User $user): void { }
    public function hashPassword(User $user, string $plainPassword): string { }
}
```

### Phase 2: Create Authentication Controllers

#### 2.1 Security Controller
Create `src/Bundle/SocialNetworkBundle/Controller/SecurityController.php`:
- `loginAction()` - Display login form
- Handle logout (configured in security.yaml)

#### 2.2 Registration Controller
Replace FOSUserBundle RegistrationController:
- `registerAction()` - Handle registration form
- `confirmAction()` - Handle email confirmation
- Remove `extends Controller` from FOSUserBundle

#### 2.3 Password Reset Controller
Replace FOSUserBundle ResettingController:
- `requestAction()` - Request password reset
- `resetAction()` - Handle password reset with token

#### 2.4 Profile Controller
Replace FOSUserBundle ProfileController:
- `showAction()` - Display user profile (already exists)
- `editAction()` - Edit user profile
- Remove `extends Controller` from FOSUserBundle

### Phase 3: Update Forms

#### 3.1 Registration Form
Update `src/Bundle/SocialNetworkBundle/Form/Type/RegistrationFormType.php`:
- Remove `extends BaseType` from FOSUserBundle
- Extend `AbstractType` directly
- Add all necessary fields

#### 3.2 Profile Form
Update `src/Bundle/SocialNetworkBundle/Form/Type/ProfileFormType.php`:
- Remove `extends BaseType` from FOSUserBundle
- Extend `AbstractType` directly
- Handle password change separately

#### 3.3 Resetting Form
Update `src/Bundle/SocialNetworkBundle/Form/Type/ResettingFormType.php`:
- Remove `extends BaseType` from FOSUserBundle
- Extend `AbstractType` directly

### Phase 4: Update Security Configuration

Update `config/packages/security.yaml`:

```yaml
security:
    password_hashers:
        Kiboko\Bundle\SocialNetworkBundle\Entity\User:
            algorithm: auto

    providers:
        app_user_provider:
            entity:
                class: Kiboko\Bundle\SocialNetworkBundle\Entity\User
                property: username

    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                enable_csrf: true
            logout:
                path: app_logout
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800

    access_control:
        - { path: ^/login, roles: PUBLIC_ACCESS }
        - { path: ^/register, roles: PUBLIC_ACCESS }
        - { path: ^/resetting, roles: PUBLIC_ACCESS }
```

### Phase 5: Create Routing

Replace FOSUserBundle routes in `Resources/config/routing/user.yml`:

```yaml
# Remove FOSUserBundle imports
# fos_user_security:
#     resource: "@FOSUserBundle/Resources/config/routing/security.xml"

# Add custom routes
app_login:
    path: /login
    defaults: { _controller: 'Kiboko\Bundle\SocialNetworkBundle\Controller\SecurityController::loginAction' }

app_logout:
    path: /logout
    methods: GET

app_register:
    path: /register
    defaults: { _controller: 'Kiboko\Bundle\SocialNetworkBundle\Controller\RegistrationController::registerAction' }

app_register_confirm:
    path: /register/confirm/{token}
    defaults: { _controller: 'Kiboko\Bundle\SocialNetworkBundle\Controller\RegistrationController::confirmAction' }

app_resetting_request:
    path: /resetting/request
    defaults: { _controller: 'Kiboko\Bundle\SocialNetworkBundle\Controller\ResettingController::requestAction' }

app_resetting_reset:
    path: /resetting/reset/{token}
    defaults: { _controller: 'Kiboko\Bundle\SocialNetworkBundle\Controller\ResettingController::resetAction' }
```

### Phase 6: Update Email System

#### 6.1 Registration Emails
Already migrated! The bundle has:
- `FosMailer.php` - Handles registration emails
- Uses Symfony Mailer (migrated from SwiftMailer)

#### 6.2 Email Templates
Templates exist at `Resources/views/Emails/`:
- Registration confirmation
- Password reset
- Welcome message

### Phase 7: Update Service Definitions

Update `Resources/config/services.yml`:

```yaml
services:
    # User Manager
    Kiboko\Bundle\SocialNetworkBundle\Service\UserManager:
        arguments:
            $entityManager: '@doctrine.orm.entity_manager'
            $passwordHasher: '@security.user_password_hasher'

    # Alias for backward compatibility
    fos_user.user_manager:
        alias: Kiboko\Bundle\SocialNetworkBundle\Service\UserManager
```

### Phase 8: Update Dependencies

Update `composer.json`:

```json
{
    "require": {
        "friendsofsymfony/user-bundle": "REMOVE THIS LINE"
    }
}
```

Run: `composer remove friendsofsymfony/user-bundle`

### Phase 9: Update Controllers Using FOSUserBundle

#### Files to update:
1. `Controller/AdminUserController.php` - Replace `$this->container->get('fos_user.user_manager')`
2. `Controller/RegistrationController.php` - Complete rewrite
3. `Controller/ProfileController.php` - Remove FOSUserBundle inheritance
4. `Controller/ResettingController.php` - Complete rewrite

## Testing Strategy

1. **Unit Tests**
   - Test UserManager service
   - Test form types
   - Test User entity methods

2. **Functional Tests**
   - Registration flow
   - Login/logout
   - Password reset
   - Profile update

3. **Integration Tests**
   - Email sending
   - Token generation and validation
   - Session management

## Migration Checklist

- [ ] Phase 1: User Management System
  - [ ] Update User entity
  - [ ] Create User repository
  - [ ] Create UserManager service

- [ ] Phase 2: Authentication Controllers
  - [ ] SecurityController
  - [ ] RegistrationController
  - [ ] ResettingController
  - [ ] ProfileController

- [ ] Phase 3: Update Forms
  - [ ] RegistrationFormType
  - [ ] ProfileFormType
  - [ ] ResettingFormType

- [ ] Phase 4: Security Configuration
  - [ ] Update security.yaml
  - [ ] Configure password hashers
  - [ ] Configure firewalls

- [ ] Phase 5: Routing
  - [ ] Create custom routes
  - [ ] Remove FOSUserBundle routes

- [ ] Phase 6: Email System
  - [ ] ✅ Already migrated to Symfony Mailer
  - [ ] Update email templates if needed

- [ ] Phase 7: Services
  - [ ] Define UserManager service
  - [ ] Update service references

- [ ] Phase 8: Dependencies
  - [ ] Remove FOSUserBundle from composer.json
  - [ ] Run composer remove

- [ ] Phase 9: Controller Updates
  - [ ] Update all FOSUserBundle service references
  - [ ] Remove FOSUserBundle inheritance

- [ ] Testing
  - [ ] Write/update unit tests
  - [ ] Write/update functional tests
  - [ ] Manual testing of all flows

## Estimated Effort

- **Small (1-2 hours)**: Phases 1, 4, 5, 7, 8
- **Medium (3-5 hours)**: Phases 2, 3, 9
- **Large (5-8 hours)**: Phase 6 (if major changes needed), Testing

**Total Estimate**: 20-30 hours of development work

## Alternative: Use Symfony MakerBundle

Symfony's MakerBundle can generate most of the authentication scaffolding:

```bash
composer require symfony/maker-bundle --dev
php bin/console make:user
php bin/console make:auth
php bin/console make:registration-form
php bin/console make:reset-password
```

Then adapt the generated code to work with the existing User entity and bundle structure.

## Risks & Considerations

1. **Data Migration**: User passwords are already hashed - ensure compatibility
2. **Session Handling**: Users will need to re-login after migration
3. **Email Templates**: Ensure all email translations are preserved
4. **Third-party Integrations**: Check if any external systems depend on FOSUserBundle routes

## Recommended Approach

Given the scope of this migration, it's recommended to:

1. **Create a feature branch** for the FOSUserBundle removal
2. **Use Symfony MakerBundle** to generate initial scaffolding
3. **Migrate incrementally** - one phase at a time
4. **Maintain backward compatibility** where possible (service aliases)
5. **Write comprehensive tests** before making changes
6. **Document all changes** for future reference

## Resources

- [Symfony Security Documentation](https://symfony.com/doc/current/security.html)
- [Making Users in Symfony](https://symfony.com/doc/current/security.html#loading-the-user-the-user-provider)
- [Symfony Authentication](https://symfony.com/doc/current/security.html#authenticating-users)
- [Password Reset Bundle](https://symfony.com/bundles/SymfonyCastsResetPasswordBundle/current/index.html)
