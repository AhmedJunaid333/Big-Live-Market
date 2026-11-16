import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../providers/auth_provider.dart';

class RegisterScreen extends ConsumerStatefulWidget {
  const RegisterScreen({super.key});

  @override
  ConsumerState<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends ConsumerState<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController(text: '+92');
  final _passwordController = TextEditingController();
  
  bool _obscurePassword = true;
  String _selectedRole = 'buyer'; // 'buyer' or 'seller'

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    final success = await ref.read(authProvider.notifier).register(
          name: _nameController.text.trim(),
          email: _emailController.text.trim(),
          password: _passwordController.text,
          phoneE164: _phoneController.text.trim().isNotEmpty && _phoneController.text.trim() != '+92'
              ? _phoneController.text.trim()
              : null,
          role: _selectedRole,
        );

    if (mounted) {
      if (success) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.secondaryMint,
            content: Text(
              'Account created successfully! Welcome to Big Live.',
              style: AppTheme.labelMd.copyWith(color: AppTheme.backgroundDark),
            ),
          ),
        );
      } else {
        final err = ref.read(authProvider).errorMessage ?? 'Registration failed';
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.error,
            content: Text(err, style: const TextStyle(color: Colors.white)),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            children: [
              // Top Bar
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 8),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_ios_new_rounded,
                      onPressed: () => Navigator.pop(context),
                      tooltip: 'Back',
                    ),
                    const SizedBox(width: 12),
                    Text(
                      'CREATE ACCOUNT',
                      style: AppTheme.labelUppercase,
                    ),
                  ],
                ),
              ),

              // Form
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 12),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
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
                        Text(
                          'Join Big Live Marketplace',
                          style: AppTheme.displayLg.copyWith(fontSize: 24),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Buy and sell verified items across Pakistan with cosmic trust.',
                          style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 24),

                        // Role Selector
                        Text('I WANT TO', style: AppTheme.labelUppercase),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: _buildRoleCard(
                                title: 'Buy Items',
                                subtitle: 'Discover & purchase',
                                role: 'buyer',
                                icon: Icons.shopping_bag_outlined,
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: _buildRoleCard(
                                title: 'Sell Items',
                                subtitle: 'Post ads & earn',
                                role: 'seller',
                                icon: Icons.storefront_outlined,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 20),

                        // Name
                        _buildInputField(
                          controller: _nameController,
                          label: 'Full Name',
                          hint: 'e.g. Tariq Mehmood',
                          icon: Icons.person_outline_rounded,
                          validator: (v) => (v == null || v.trim().length < 2)
                              ? 'Enter a valid name (min 2 characters)'
                              : null,
                        ),
                        const SizedBox(height: 14),

                        // Email
                        _buildInputField(
                          controller: _emailController,
                          label: 'Email Address',
                          hint: 'e.g. name@domain.pk',
                          icon: Icons.alternate_email_rounded,
                          keyboardType: TextInputType.emailAddress,
                          validator: (v) => (v == null || !v.contains('@'))
                              ? 'Enter a valid email address'
                              : null,
                        ),
                        const SizedBox(height: 14),

                        // Phone
                        _buildInputField(
                          controller: _phoneController,
                          label: 'Phone Number (E.164)',
                          hint: '+923001234567',
                          icon: Icons.phone_outlined,
                          keyboardType: TextInputType.phone,
                          validator: (v) {
                            if (v != null && v.trim().isNotEmpty && v.trim() != '+92') {
                              if (!RegExp(r'^\+[1-9]\d{9,14}$').hasMatch(v.trim())) {
                                return 'Valid format: +923001234567';
                              }
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 14),

                        // Password
                        _buildInputField(
                          controller: _passwordController,
                          label: 'Password',
                          hint: 'Min 8 characters',
                          icon: Icons.lock_outline_rounded,
                          obscureText: _obscurePassword,
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscurePassword ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                              color: AppTheme.onSurfaceVariant,
                              size: 20,
                            ),
                            onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                          ),
                          validator: (v) => (v == null || v.length < 8)
                              ? 'Password must be at least 8 characters'
                              : null,
                        ),
                        const SizedBox(height: 24),

                        // Submit Button
                        NebulaButtonPrimary(
                          label: 'CREATE ACCOUNT',
                          icon: Icons.arrow_forward_rounded,
                          isLoading: auth.isLoading,
                          onPressed: auth.isLoading ? null : _submit,
                        ),
                        const SizedBox(height: 16),

                        // Already have account
                        Center(
                          child: TextButton(
                            onPressed: () => Navigator.pop(context),
                            child: RichText(
                              text: TextSpan(
                                text: 'Already have an account? ',
                                style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
                                children: [
                                  TextSpan(
                                    text: 'Sign In',
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
                        const SizedBox(height: 20),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildRoleCard({
    required String title,
    required String subtitle,
    required String role,
    required IconData icon,
  }) {
    final isSelected = _selectedRole == role;
    return GestureDetector(
      onTap: () => setState(() => _selectedRole = role),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(AppTheme.radiusLg),
          color: isSelected
              ? AppTheme.primaryLavender.withValues(alpha: 0.18)
              : AppTheme.glassSurface,
          border: Border.all(
            color: isSelected ? AppTheme.primaryLavender : AppTheme.borderHairline,
            width: isSelected ? 1.5 : 1,
          ),
        ),
        child: Column(
          children: [
            Icon(
              icon,
              color: isSelected ? AppTheme.primaryLavender : AppTheme.onSurfaceVariant,
              size: 24,
            ),
            const SizedBox(height: 8),
            Text(
              title,
              style: AppTheme.labelMd.copyWith(
                color: isSelected ? AppTheme.primaryLavender : AppTheme.onSurface,
                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w600,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              subtitle,
              style: AppTheme.bodySm.copyWith(
                color: AppTheme.onSurfaceVariant,
                fontSize: 11,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInputField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    bool obscureText = false,
    Widget? suffixIcon,
    TextInputType? keyboardType,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: AppTheme.labelSm.copyWith(color: AppTheme.onSurfaceVariant)),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          obscureText: obscureText,
          keyboardType: keyboardType,
          style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
          validator: validator,
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant.withValues(alpha: 0.5)),
            prefixIcon: Icon(icon, color: AppTheme.primaryLavender, size: 20),
            suffixIcon: suffixIcon,
            filled: true,
            fillColor: AppTheme.glassSurface,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppTheme.radiusLg),
              borderSide: const BorderSide(color: AppTheme.borderHairline),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppTheme.radiusLg),
              borderSide: const BorderSide(color: AppTheme.borderHairline),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppTheme.radiusLg),
              borderSide: const BorderSide(color: AppTheme.primaryLavender, width: 1.5),
            ),
          ),
        ),
      ],
    );
  }
}
