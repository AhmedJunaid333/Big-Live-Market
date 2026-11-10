import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../auth/providers/auth_provider.dart';
import '../../auth/screens/login_screen.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('My Account'),
      ),
      body: authState.isAuthenticated && authState.user != null
          ? _buildAuthenticatedProfile(context, ref, authState)
          : _buildGuestProfile(context),
    );
  }

  Widget _buildGuestProfile(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(AppTheme.space24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const CircleAvatar(
              radius: 40,
              backgroundColor: AppTheme.borderSubtle,
              child: Icon(Icons.person, size: 48, color: AppTheme.textSecondary),
            ),
            const SizedBox(height: AppTheme.space16),
            const Text(
              'Log in to your account',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.primaryNavy),
            ),
            const SizedBox(height: AppTheme.space8),
            const Text(
              'Manage your listings, chat with buyers, and track verified purchases.',
              textAlign: TextAlign.center,
              style: TextStyle(color: AppTheme.textSecondary, fontSize: 14),
            ),
            const SizedBox(height: AppTheme.space24),
            ElevatedButton(
              onPressed: () {
                Navigator.of(context).push(
                  MaterialTranslucentRoute(builder: (_) => const LoginScreen()),
                );
              },
              child: const Text('Sign In / Register'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAuthenticatedProfile(BuildContext context, WidgetRef ref, AuthState authState) {
    final user = authState.user!;

    return ListView(
      padding: const EdgeInsets.all(AppTheme.space16),
      children: [
        Card(
          child: Padding(
            padding: const EdgeInsets.all(AppTheme.space16),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 32,
                  backgroundColor: AppTheme.brandBlue.withOpacity(0.1),
                  child: Text(
                    user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                    style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: AppTheme.brandBlue),
                  ),
                ),
                const SizedBox(width: AppTheme.space16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        user.name,
                        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primaryNavy),
                      ),
                      const SizedBox(height: AppTheme.space4),
                      Text(
                        user.email ?? user.phoneE164 ?? '',
                        style: const TextStyle(color: AppTheme.textSecondary, fontSize: 13),
                      ),
                      const SizedBox(height: AppTheme.space8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: AppTheme.brandBlue.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(AppTheme.radiusSmall),
                        ),
                        child: Text(
                          user.roles.isNotEmpty ? user.roles.first.toUpperCase() : 'BUYER',
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.brandBlue),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: AppTheme.space16),
        ListTile(
          leading: const Icon(Icons.verified_user_outlined, color: AppTheme.brandBlue),
          title: const Text('Account Verification'),
          subtitle: const Text('Submit CNIC or Trade License for verified trust badge'),
          trailing: const Icon(Icons.chevron_right),
          onTap: () {},
        ),
        const Divider(),
        ListTile(
          leading: const Icon(Icons.devices_outlined, color: AppTheme.brandBlue),
          title: const Text('Security & Authorized Devices'),
          trailing: const Icon(Icons.chevron_right),
          onTap: () {},
        ),
        const Divider(),
        ListTile(
          leading: const Icon(Icons.description_outlined, color: AppTheme.brandBlue),
          title: const Text('Privacy & Consent'),
          trailing: const Icon(Icons.chevron_right),
          onTap: () {},
        ),
        const Divider(),
        ListTile(
          leading: const Icon(Icons.logout, color: AppTheme.errorRed),
          title: const Text('Sign Out', style: TextStyle(color: AppTheme.errorRed)),
          onTap: () async {
            await ref.read(authProvider.notifier).logout();
          },
        ),
      ],
    );
  }
}

class MaterialTranslucentRoute<T> extends MaterialPageRoute<T> {
  MaterialTranslucentRoute({required super.builder});
}
