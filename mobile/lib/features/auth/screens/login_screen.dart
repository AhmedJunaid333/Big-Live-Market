import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../providers/auth_provider.dart';
import 'register_screen.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _emailController = TextEditingController(text: 'admin@biglive.pk');
  final _passwordController = TextEditingController(text: 'Admin@12345');
  final _formKey = GlobalKey<FormState>();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    final success = await ref.read(authProvider.notifier).login(
      _emailController.text.trim(),
      _passwordController.text,
    );

    if (success && mounted) {
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: AppTheme.backgroundMid,
          content: Text(
            'Logged in successfully!',
            style: AppTheme.bodyMd.copyWith(color: AppTheme.secondaryMint),
          ),
        ),
      );
    }
  }

  void _fillCredentials(String email, String password, String role) {
    setState(() {
      _emailController.text = email;
      _passwordController.text = password;
    });
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        duration: const Duration(seconds: 1),
        backgroundColor: AppTheme.backgroundMid,
        content: Text(
          '$role credentials filled ($email)',
          style: AppTheme.bodySm.copyWith(color: AppTheme.primaryLavender),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: Padding(
          padding: const EdgeInsets.all(8.0),
          child: NebulaGlassIconButton(
            icon: Icons.arrow_back_rounded,
            color: AppTheme.onSurface,
            onPressed: () => Navigator.of(context).pop(),
          ),
        ),
        title: Text(
          'Sign In',
          style: AppTheme.headlineMd.copyWith(color: AppTheme.onSurface),
        ),
      ),
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(AppTheme.gutter),
              child: Form(
                key: _formKey,
                child: NebulaGlassCard(
                  padding: const EdgeInsets.all(AppTheme.cardPadding),
                  borderRadius: AppTheme.radius2Xl,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Header
                      Center(
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(24),
                          child: Image.asset(
                            'assets/icons/app_logo.png',
                            height: 96,
                            width: 96,
                            fit: BoxFit.cover,
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Center(
                        child: Text(
                          'Welcome Back',
                          style: AppTheme.headlineMd.copyWith(
                            fontWeight: FontWeight.w700,
                            color: AppTheme.onSurface,
                          ),
                        ),
                      ),
                      const SizedBox(height: 6),
                      Center(
                        child: Text(
                          'Sign in to your Big Live cosmos account',
                          style: AppTheme.bodySm.copyWith(
                            color: AppTheme.onSurfaceVariant,
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Error message banner
                      if (authState.errorMessage != null) ...[
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: AppTheme.error.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(AppTheme.radiusSm),
                            border: Border.all(color: AppTheme.error.withValues(alpha: 0.4)),
                          ),
                          child: Text(
                            authState.errorMessage!,
                            style: AppTheme.bodySm.copyWith(color: AppTheme.error),
                          ),
                        ),
                        const SizedBox(height: 16),
                      ],

                      // Email input
                      Text(
                        'EMAIL ADDRESS',
                        style: AppTheme.labelUppercase,
                      ),
                      const SizedBox(height: 6),
                      Container(
                        decoration: BoxDecoration(
                          color: AppTheme.surfaceGlass,
                          borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                          border: Border.all(color: AppTheme.borderHairline),
                        ),
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: TextFormField(
                          controller: _emailController,
                          keyboardType: TextInputType.emailAddress,
                          style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                          validator: (v) => (v == null || v.isEmpty) ? 'Please enter email' : null,
                          decoration: InputDecoration(
                            hintText: 'user@example.com',
                            hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                            prefixIcon: const Icon(
                              Icons.email_rounded,
                              size: 18,
                              color: AppTheme.primaryLavender,
                            ),
                            border: InputBorder.none,
                          ),
                        ),
                      ),

                      const SizedBox(height: 16),

                      // Password input
                      Text(
                        'PASSWORD',
                        style: AppTheme.labelUppercase,
                      ),
                      const SizedBox(height: 6),
                      Container(
                        decoration: BoxDecoration(
                          color: AppTheme.surfaceGlass,
                          borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                          border: Border.all(color: AppTheme.borderHairline),
                        ),
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: TextFormField(
                          controller: _passwordController,
                          obscureText: _obscurePassword,
                          style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                          validator: (v) => (v == null || v.isEmpty) ? 'Please enter password' : null,
                          decoration: InputDecoration(
                            hintText: '••••••••',
                            hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                            prefixIcon: const Icon(
                              Icons.key_rounded,
                              size: 18,
                              color: AppTheme.primaryLavender,
                            ),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _obscurePassword ? Icons.visibility_off_rounded : Icons.visibility_rounded,
                                size: 18,
                                color: AppTheme.onSurfaceMuted,
                              ),
                              onPressed: () {
                                setState(() {
                                  _obscurePassword = !_obscurePassword;
                                });
                              },
                            ),
                            border: InputBorder.none,
                          ),
                        ),
                      ),

                      const SizedBox(height: AppTheme.sectionGap),

                      // Sign In Primary Button
                      NebulaButtonPrimary(
                        label: 'Sign In',
                        isLoading: authState.isLoading,
                        onPressed: _handleLogin,
                      ),

                      const SizedBox(height: 24),
                      const Divider(color: AppTheme.borderHairline),
                      const SizedBox(height: 16),

                      Text(
                        'DEMO ROLES (1-TAP FILL)',
                        style: AppTheme.labelUppercase.copyWith(
                          color: AppTheme.onSurfaceMuted,
                        ),
                      ),
                      const SizedBox(height: 10),
                      Row(
                        children: [
                          Expanded(
                            child: _buildRoleChip(
                              label: 'Admin',
                              color: AppTheme.accentPink,
                              onTap: () => _fillCredentials('admin@biglive.pk', 'Admin@12345', 'Admin'),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: _buildRoleChip(
                              label: 'Seller',
                              color: AppTheme.secondaryMint,
                              onTap: () => _fillCredentials('seller@biglive.pk', 'Seller@12345', 'Seller'),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: _buildRoleChip(
                              label: 'Buyer',
                              color: AppTheme.accentBlue,
                              onTap: () => _fillCredentials('buyer@biglive.pk', 'Buyer@12345', 'Buyer'),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 18),
                      Center(
                        child: TextButton(
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(builder: (_) => const RegisterScreen()),
                            );
                          },
                          child: RichText(
                            text: TextSpan(
                              text: "Don't have an account? ",
                              style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
                              children: [
                                TextSpan(
                                  text: 'Sign Up',
                                  style: AppTheme.bodyMd.copyWith(
                                    color: AppTheme.accentCyan,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRoleChip({
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 8),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.15),
          borderRadius: BorderRadius.circular(AppTheme.radiusSm),
          border: Border.all(
            color: color.withValues(alpha: 0.3),
          ),
        ),
        alignment: Alignment.center,
        child: Text(
          label,
          style: AppTheme.labelUppercase.copyWith(
            color: color,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
