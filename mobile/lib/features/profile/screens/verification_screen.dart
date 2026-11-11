import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';

class VerificationScreen extends ConsumerStatefulWidget {
  const VerificationScreen({super.key});

  @override
  ConsumerState<VerificationScreen> createState() => _VerificationScreenState();
}

class _VerificationScreenState extends ConsumerState<VerificationScreen> {
  final _formKey = GlobalKey<FormState>();
  final _docNumberController = TextEditingController();
  String _docType = 'cnic'; // 'cnic', 'passport', 'trade_license'
  XFile? _frontImage;
  XFile? _backImage;
  bool _isSubmitting = false;
  String _status = 'unverified'; // 'unverified', 'pending', 'verified'

  @override
  void dispose() {
    _docNumberController.dispose();
    super.dispose();
  }

  Future<void> _pickImage(bool isFront) async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.gallery, imageQuality: 85);
    if (picked != null) {
      setState(() {
        if (isFront) {
          _frontImage = picked;
        } else {
          _backImage = picked;
        }
      });
    }
  }

  Future<void> _submitVerification() async {
    if (!_formKey.currentState!.validate()) return;
    if (_frontImage == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          backgroundColor: AppTheme.error,
          content: Text('Please upload document front side photo'),
        ),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final client = ref.read(apiClientProvider);
      await client.dio.post(
        '/me/verification',
        data: {
          'type': _docType,
          'evidence_private_path': 'verifications/${_docType}_${DateTime.now().millisecondsSinceEpoch}.jpg',
        },
      );

      setState(() => _status = 'pending');

      if (mounted) {
        showDialog(
          context: context,
          builder: (ctx) => AlertDialog(
            backgroundColor: AppTheme.backgroundMid,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
              side: const BorderSide(color: AppTheme.borderHairline),
            ),
            title: Row(
              children: [
                const Icon(Icons.verified_rounded, color: AppTheme.secondaryMint),
                const SizedBox(width: 8),
                Text('Request Submitted', style: AppTheme.headlineMd),
              ],
            ),
            content: Text(
              'Your identity verification request has been queued for trust review. You will receive a notification once verified.',
              style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
            ),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  Navigator.pop(context);
                },
                child: Text('OK', style: AppTheme.labelMd.copyWith(color: AppTheme.primaryLavender)),
              ),
            ],
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.error,
            content: Text('Verification submission: $e'),
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _isSubmitting = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
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
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('TRUST & SAFETY', style: AppTheme.labelUppercase),
                        Text('Account Verification', style: AppTheme.headlineMd),
                      ],
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
                        // Status Card
                        NebulaGlassCard(
                          child: Row(
                            children: [
                              TintedIconBubble(
                                icon: _status == 'verified'
                                    ? Icons.verified_user_rounded
                                    : _status == 'pending'
                                        ? Icons.hourglass_top_rounded
                                        : Icons.shield_outlined,
                                tint: _status == 'verified'
                                    ? AppTheme.secondaryMint
                                    : _status == 'pending'
                                        ? AppTheme.primaryLavender
                                        : AppTheme.accentCyan,
                              ),
                              const SizedBox(width: 14),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      _status == 'verified'
                                          ? 'Verified Account'
                                          : _status == 'pending'
                                              ? 'Verification In Review'
                                              : 'Unverified Account',
                                      style: AppTheme.headlineMd,
                                    ),
                                    const SizedBox(height: 2),
                                    Text(
                                      _status == 'verified'
                                          ? 'You hold a verified seller trust badge.'
                                          : _status == 'pending'
                                              ? 'Documents submitted, pending review.'
                                              : 'Verify identity for 5x more buyer inquiries.',
                                      style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 20),

                        // Document Type
                        Text('DOCUMENT TYPE', style: AppTheme.labelUppercase),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: _buildDocTypeCard(
                                label: 'CNIC',
                                type: 'cnic',
                                icon: Icons.badge_outlined,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: _buildDocTypeCard(
                                label: 'Passport',
                                type: 'passport',
                                icon: Icons.menu_book_outlined,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: _buildDocTypeCard(
                                label: 'Trade License',
                                type: 'trade_license',
                                icon: Icons.business_center_outlined,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 18),

                        // Document Number
                        Text('DOCUMENT ID NUMBER', style: AppTheme.labelUppercase),
                        const SizedBox(height: 8),
                        TextFormField(
                          controller: _docNumberController,
                          style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                          validator: (v) => (v == null || v.trim().length < 5)
                              ? 'Enter a valid document identification number'
                              : null,
                          decoration: InputDecoration(
                            hintText: _docType == 'cnic' ? 'e.g. 35201-1234567-1' : 'Document Number',
                            hintStyle: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant.withValues(alpha: 0.5)),
                            prefixIcon: const Icon(Icons.pin_outlined, color: AppTheme.primaryLavender, size: 20),
                            filled: true,
                            fillColor: AppTheme.glassSurface,
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(AppTheme.radiusLg),
                              borderSide: const BorderSide(color: AppTheme.borderHairline),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(AppTheme.radiusLg),
                              borderSide: const BorderSide(color: AppTheme.borderHairline),
                            ),
                          ),
                        ),
                        const SizedBox(height: 20),

                        // Document Photos
                        Text('DOCUMENT PHOTOS', style: AppTheme.labelUppercase),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: _buildPhotoSlot(
                                label: 'Front Side',
                                file: _frontImage,
                                onTap: () => _pickImage(true),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: _buildPhotoSlot(
                                label: 'Back Side (Optional)',
                                file: _backImage,
                                onTap: () => _pickImage(false),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 28),

                        // Submit CTA
                        NebulaButtonPrimary(
                          label: 'SUBMIT FOR VERIFICATION',
                          icon: Icons.shield_rounded,
                          isLoading: _isSubmitting,
                          onPressed: _isSubmitting ? null : _submitVerification,
                        ),
                        const SizedBox(height: 24),
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

  Widget _buildDocTypeCard({
    required String label,
    required String type,
    required IconData icon,
  }) {
    final isSelected = _docType == type;
    return GestureDetector(
      onTap: () => setState(() => _docType = type),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 8),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(AppTheme.radiusLg),
          color: isSelected
              ? AppTheme.primaryLavender.withValues(alpha: 0.2)
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
              size: 22,
              color: isSelected ? AppTheme.primaryLavender : AppTheme.onSurfaceVariant,
            ),
            const SizedBox(height: 6),
            Text(
              label,
              style: AppTheme.labelSm.copyWith(
                color: isSelected ? AppTheme.primaryLavender : AppTheme.onSurface,
                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPhotoSlot({
    required String label,
    required XFile? file,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        height: 120,
        decoration: BoxDecoration(
          color: AppTheme.glassSurface,
          borderRadius: BorderRadius.circular(AppTheme.radiusLg),
          border: Border.all(
            color: file != null ? AppTheme.secondaryMint : AppTheme.borderHairline,
            width: file != null ? 1.5 : 1,
          ),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              file != null ? Icons.check_circle_outline_rounded : Icons.camera_alt_outlined,
              color: file != null ? AppTheme.secondaryMint : AppTheme.primaryLavender,
              size: 28,
            ),
            const SizedBox(height: 8),
            Text(
              file != null ? 'Uploaded' : label,
              style: AppTheme.labelSm.copyWith(
                color: file != null ? AppTheme.secondaryMint : AppTheme.onSurfaceVariant,
                fontSize: 11,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}
