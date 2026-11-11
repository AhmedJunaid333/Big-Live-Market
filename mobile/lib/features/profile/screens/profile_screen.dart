import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../auth/providers/auth_provider.dart';
import '../../auth/screens/login_screen.dart';
import '../../listings/screens/favorites_screen.dart';
import '../../listings/screens/my_listings_screen.dart';
import 'devices_screen.dart';
import 'verification_screen.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);
    final lang = ref.watch(languageProvider);

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
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'ACCOUNT & PREFERENCES',
                          style: AppTheme.labelUppercase,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          AppStrings.tabProfile,
                          style: AppTheme.displayLg,
                        ),
                      ],
                    ),
                    // Quick Language Switcher Pill
                    GestureDetector(
                      onTap: () => ref.read(languageProvider.notifier).toggleLanguage(),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryLavender.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                          border: Border.all(color: AppTheme.primaryLavender.withValues(alpha: 0.35)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.language_rounded, size: 14, color: AppTheme.primaryLavender),
                            const SizedBox(width: 6),
                            Text(
                              lang == AppLanguage.ur ? 'اردو' : 'EN',
                              style: AppTheme.labelSm.copyWith(
                                color: AppTheme.primaryLavender,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // Profile Content Body
              Expanded(
                child: authState.isAuthenticated && authState.user != null
                    ? _buildAuthenticatedProfile(context, ref, authState, lang)
                    : _buildGuestProfile(context, ref, lang),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildGuestProfile(BuildContext context, WidgetRef ref, AppLanguage lang) {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(
          AppTheme.gutter,
          16,
          AppTheme.gutter,
          120, // Clear floating bottom tab bar
        ),
        child: Column(
          children: [
            NebulaGlassCard(
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
                    'Manage your listings, bookmark favorites, and chat with buyers in real time.',
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
            const SizedBox(height: 16),
            // Quick guest options
            NebulaGlassCard(
              padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 16),
              child: Column(
                children: [
                  _buildMenuRow(
                    icon: Icons.favorite_rounded,
                    accentColor: AppTheme.accentPink,
                    title: AppStrings.favorites,
                    subtitle: 'View your bookmarked advertisements',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const FavoritesScreen()),
                      );
                    },
                  ),
                  const Divider(color: AppTheme.borderHairline),
                  _buildMenuRow(
                    icon: Icons.language_rounded,
                    accentColor: AppTheme.accentCyan,
                    title: AppStrings.languageSetting,
                    subtitle: lang == AppLanguage.ur ? 'اردو فعال ہے (تبدیل کریں)' : 'English active (Switch to Urdu)',
                    onTap: () => ref.read(languageProvider.notifier).toggleLanguage(),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAuthenticatedProfile(
    BuildContext context,
    WidgetRef ref,
    AuthState authState,
    AppLanguage lang,
  ) {
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
                      text: user.roles.isNotEmpty ? user.roles.first.toUpperCase() : 'BUYER',
                      tone: NebulaBadgeTone.mint,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),

        const SizedBox(height: 16),

        // Quick Stats Tiles
        Row(
          children: [
            Expanded(
              child: GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const MyListingsScreen()),
                  );
                },
                child: NebulaGlassCard(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    children: [
                      Text(
                        'Ads',
                        style: AppTheme.numeralLg.copyWith(
                          color: AppTheme.primaryLavender,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'My Listings',
                        style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const FavoritesScreen()),
                  );
                },
                child: NebulaGlassCard(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    children: [
                      const Icon(Icons.favorite_rounded, color: AppTheme.accentPink, size: 28),
                      const SizedBox(height: 4),
                      Text(
                        'Favorites',
                        style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                      ),
                    ],
                  ),
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
                      'Trust Rating',
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
                icon: Icons.storefront_outlined,
                accentColor: AppTheme.primaryLavender,
                title: AppStrings.myListings,
                subtitle: 'Manage, pause, or mark your listings as sold',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const MyListingsScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.favorite_border_rounded,
                accentColor: AppTheme.accentPink,
                title: AppStrings.favorites,
                subtitle: 'View saved & bookmarked advertisements',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const FavoritesScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.verified_user_rounded,
                accentColor: AppTheme.secondaryMint,
                title: AppStrings.accountVerification,
                subtitle: 'Submit CNIC or Trade License for trust badge',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const VerificationScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.shield_rounded,
                accentColor: AppTheme.accentCyan,
                title: AppStrings.securitySessions,
                subtitle: 'Manage active devices and sessions',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const DevicesScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.language_rounded,
                accentColor: AppTheme.accentCyan,
                title: AppStrings.languageSetting,
                subtitle: lang == AppLanguage.ur ? 'اردو فعال ہے (تبدیل کریں)' : 'English active (Switch to Urdu)',
                onTap: () => ref.read(languageProvider.notifier).toggleLanguage(),
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.logout_rounded,
                accentColor: AppTheme.accentPink,
                title: AppStrings.signOut,
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
              tint: accentColor,
              size: 40,
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
                      color: isDestructive ? AppTheme.accentPink : AppTheme.onSurface,
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
            const Icon(
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
