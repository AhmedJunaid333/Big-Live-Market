import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _emailController = TextEditingController(text: 'admin@biglive.pk');
  final _passwordController = TextEditingController(text: 'Admin@12345');
  final _formKey = GlobalKey<FormState>();

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
        const SnackBar(content: Text('Logged in successfully!')),
      );
    }
  }

  void _fillCredentials(String email, String password) {
    setState(() {
      _emailController.text = email;
      _passwordController.text = password;
    });
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Sign In to Big Live'),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(AppTheme.space24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const SizedBox(height: AppTheme.space8),
                const Center(
                  child: Text(
                    'Welcome to Big Live',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.primaryNavy,
                    ),
                  ),
                ),
                const SizedBox(height: AppTheme.space8),
                const Center(
                  child: Text(
                    'Enter your credentials or choose a demo role below',
                    style: TextStyle(color: AppTheme.textSecondary, fontSize: 13),
                  ),
                ),
                const SizedBox(height: AppTheme.space24),
                if (authState.errorMessage != null)
                  Container(
                    margin: const EdgeInsets.only(bottom: AppTheme.space16),
                    padding: const EdgeInsets.all(AppTheme.space12),
                    decoration: BoxDecoration(
                      color: AppTheme.errorRed.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(AppTheme.radiusSmall),
                      border: Border.all(color: AppTheme.errorRed.withValues(alpha: 0.3)),
                    ),
                    child: Text(
                      authState.errorMessage!,
                      style: const TextStyle(color: AppTheme.errorRed, fontSize: 13),
                    ),
                  ),
                TextFormField(
                  controller: _emailController,
                  decoration: const InputDecoration(
                    labelText: 'Email Address',
                    prefixIcon: Icon(Icons.email_outlined),
                  ),
                  keyboardType: TextInputType.emailAddress,
                  validator: (val) {
                    if (val == null || val.isEmpty) return 'Please enter your email';
                    return null;
                  },
                ),
                const SizedBox(height: AppTheme.space16),
                TextFormField(
                  controller: _passwordController,
                  decoration: const InputDecoration(
                    labelText: 'Password',
                    prefixIcon: Icon(Icons.lock_outline),
                  ),
                  obscureText: true,
                  validator: (val) {
                    if (val == null || val.isEmpty) return 'Please enter your password';
                    return null;
                  },
                ),
                const SizedBox(height: AppTheme.space24),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.champagne,
                    foregroundColor: AppTheme.obsidian,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                  ),
                  onPressed: authState.isLoading ? null : _handleLogin,
                  child: authState.isLoading
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.obsidian),
                        )
                      : const Text('Sign In', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                ),

                const SizedBox(height: AppTheme.space32),
                const Divider(),
                const SizedBox(height: AppTheme.space12),
                const Text(
                  'Quick 1-Tap Demo Credentials:',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppTheme.textPrimary),
                ),
                const SizedBox(height: AppTheme.space8),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    ActionChip(
                      avatar: const Icon(Icons.security, size: 16, color: AppTheme.obsidian),
                      label: const Text('Admin'),
                      onPressed: () => _fillCredentials('admin@biglive.pk', 'Admin@12345'),
                    ),
                    ActionChip(
                      avatar: const Icon(Icons.storefront, size: 16, color: AppTheme.obsidian),
                      label: const Text('Seller'),
                      onPressed: () => _fillCredentials('seller@biglive.pk', 'Seller@12345'),
                    ),
                    ActionChip(
                      avatar: const Icon(Icons.person, size: 16, color: AppTheme.obsidian),
                      label: const Text('Buyer'),
                      onPressed: () => _fillCredentials('buyer@biglive.pk', 'Buyer@12345'),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
