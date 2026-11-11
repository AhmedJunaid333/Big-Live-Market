import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../auth/providers/auth_provider.dart';
import '../../auth/screens/login_screen.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic Header
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 16, AppTheme.gutter, 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'ACCOUNT & PREFERENCES',
                      style: AppTheme.labelUppercase,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Profile',
                      style: AppTheme.displayLg,
                    ),
                  ],
                ),
              ),

              // Profile Content Body
              Expanded(
                child: authState.isAuthenticated && authState.user != null
                    ? _buildAuthenticatedProfile(context, ref, authState)
                    : _buildGuestProfile(context),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildGuestProfile(BuildContext context) {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(
          AppTheme.gutter,
          16,
          AppTheme.gutter,
          120, // Clear floating bottom tab bar
        ),
        child: NebulaGlassCard(
          padding: const EdgeInsets.all(AppTheme.cardPadding),
          borderRadius: AppTheme.radius2Xl,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const NebulaAvatar(
                size: 72,
                initials: '?',
              ),
              const SizedBox(height: 20),
              Text(
                'Log in to your account',
                style: AppTheme.headlineMd.copyWith(
                  fontWeight: FontWeight.w700,
                  color: AppTheme.onSurface,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                'Manage your listings, chat with buyers, and track verified purchases in real time.',
                textAlign: TextAlign.center,
                style: AppTheme.bodySm.copyWith(
                  color: AppTheme.onSurfaceVariant,
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 24),
              NebulaButtonPrimary(
                label: 'Sign In / Register',
                onPressed: () {
                  Navigator.of(context).push(
                    MaterialPageRoute(builder: (_) => const LoginScreen()),
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildAuthenticatedProfile(BuildContext context, WidgetRef ref, AuthState authState) {
    final user = authState.user!;

    return ListView(
      padding: const EdgeInsets.fromLTRB(
        AppTheme.gutter,
        8,
        AppTheme.gutter,
        120, // Clear floating bottom tab bar
      ),
      children: [
        // User Identity Card
        NebulaGlassCard(
          padding: const EdgeInsets.all(20),
          child: Row(
            children: [
              NebulaAvatar(
                size: 58,
                initials: user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                isOnline: true,
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      user.name,
                      style: AppTheme.bodyMd.copyWith(
                        fontWeight: FontWeight.w700,
                        fontSize: 17,
                        color: AppTheme.onSurface,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      user.email ?? user.phoneE164 ?? '',
                      style: AppTheme.bodySm.copyWith(
                        color: AppTheme.onSurfaceVariant,
                      ),
                    ),
                    const SizedBox(height: 8),
                    NebulaBadgePill(
                      label: user.roles.isNotEmpty ? user.roles.first : 'BUYER',
                      color: AppTheme.secondaryMint,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),

        const SizedBox(height: 16),

        // Quick Stats Tiles (Active Ads, Messages, Rating)
        Row(
          children: [
            Expanded(
              child: NebulaGlassCard(
                padding: const EdgeInsets.all(14),
                child: Column(
                  children: [
                    Text(
                      '3',
                      style: AppTheme.numeralLg.copyWith(
                        color: AppTheme.primaryLavender,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Active Ads',
                      style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: NebulaGlassCard(
                padding: const EdgeInsets.all(14),
                child: Column(
                  children: [
                    Text(
                      '12',
                      style: AppTheme.numeralLg.copyWith(
                        color: AppTheme.secondaryMint,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Inquiries',
                      style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: NebulaGlassCard(
                padding: const EdgeInsets.all(14),
                child: Column(
                  children: [
                    Text(
                      '5.0★',
                      style: AppTheme.numeralLg.copyWith(
                        color: AppTheme.accentAmber,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Rating',
                      style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),

        const SizedBox(height: 16),

        // Menu Items List
        NebulaGlassCard(
          padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
          child: Column(
            children: [
              _buildMenuRow(
                icon: Icons.verified_user_rounded,
                accentColor: AppTheme.secondaryMint,
                title: 'Account Verification',
                subtitle: 'Submit CNIC or Trade License for trust badge',
                onTap: () {},
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.shield_rounded,
                accentColor: AppTheme.accentIndigo,
                title: 'Security & Sessions',
                subtitle: 'Manage devices and two-factor auth',
                onTap: () {},
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.privacy_tip_rounded,
                accentColor: AppTheme.accentPink,
                title: 'Privacy & Data Consent',
                subtitle: 'Data usage preferences and policies',
                onTap: () {},
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.logout_rounded,
                accentColor: AppTheme.accentRose,
                title: 'Sign Out',
                subtitle: 'Disconnect this mobile device session',
                isDestructive: true,
                onTap: () async {
                  await ref.read(authProvider.notifier).logout();
                },
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildMenuRow({
    required IconData icon,
    required Color accentColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
    bool isDestructive = false,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(AppTheme.radiusSm),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 12),
        child: Row(
          children: [
            TintedIconBubble(
              icon: icon,
              accentColor: accentColor,
              size: 40,
              iconSize: 20,
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: AppTheme.bodyMd.copyWith(
                      fontWeight: FontWeight.w700,
                      color: isDestructive ? AppTheme.accentRose : AppTheme.onSurface,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    subtitle,
                    style: AppTheme.caption.copyWith(
                      color: AppTheme.onSurfaceMuted,
                    ),
                  ),
                ],
              ),
            ),
            Icon(
              Icons.chevron_right_rounded,
              color: AppTheme.onSurfaceMuted,
              size: 20,
            ),
          ],
        ),
      ),
    );
  }
}
