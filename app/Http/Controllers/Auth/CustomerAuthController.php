<?php

namespace App\Http\Controllers\Auth;

/**
 * Dedicated Customer Authentication Controller (Architectural Alias for AuthController).
 *
 * Dedicated strictly to customer-level authentication, registration,
 * and session management under Phase 16 multi-portal architecture.
 */
class CustomerAuthController extends AuthController
{
    // Inherits all strict customer guard methods, registration, and logout from AuthController.
}

