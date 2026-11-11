import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';

class PostAdScreen extends ConsumerStatefulWidget {
  const PostAdScreen({super.key});

  @override
  ConsumerState<PostAdScreen> createState() => _PostAdScreenState();
}

class _PostAdScreenState extends ConsumerState<PostAdScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _priceController = TextEditingController();
  final _locationController = TextEditingController(text: 'Gulberg III, Lahore');
  final _descController = TextEditingController();

  int _selectedCategory = 1;
  final int _selectedCity = 1;
  String _selectedCondition = 'used';
  bool _isSubmitting = false;
  final List<XFile> _selectedImages = [];

  final List<Map<String, dynamic>> _categories = const [
    {'id': 1, 'name': 'Motors'},
    {'id': 2, 'name': 'Mobiles'},
    {'id': 3, 'name': 'Property'},
    {'id': 4, 'name': 'Tech'},
    {'id': 5, 'name': 'Bikes'},
  ];

  @override
  void dispose() {
    _titleController.dispose();
    _priceController.dispose();
    _locationController.dispose();
    _descController.dispose();
    super.dispose();
  }

  Future<void> _pickImages() async {
    try {
      final picker = ImagePicker();
      final picked = await picker.pickMultiImage(imageQuality: 85);
      if (picked.isNotEmpty) {
        setState(() {
          _selectedImages.addAll(picked);
        });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.backgroundMid,
            content: Text('Could not open gallery: $e'),
          ),
        );
      }
    }
  }

  Future<void> _submitAd() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);

    try {
      final client = ref.read(apiClientProvider);
      final price = double.tryParse(_priceController.text) ?? 50000;

      final res = await client.dio.post(
        '/listings',
        data: {
          'title': _titleController.text.trim(),
          'category_id': _selectedCategory,
          'city_id': _selectedCity,
          'price_minor': (price * 100).toInt(),
          'currency': 'PKR',
          'condition_code': _selectedCondition,
          'location_label': _locationController.text.trim(),
          'description': _descController.text.trim(),
        },
      );

      final listingId = res.data['data']?['id'];
      if (listingId != null && _selectedImages.isNotEmpty) {
        for (final img in _selectedImages) {
          try {
            final formData = FormData.fromMap({
              'image': await MultipartFile.fromFile(img.path, filename: img.name),
            });
            await client.dio.post('/listings/$listingId/media', data: formData);
          } catch (_) {}
        }
      }

      ref.invalidate(listingsFeedProvider);

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
                const Icon(Icons.check_circle_rounded, color: AppTheme.secondaryMint),
                const SizedBox(width: 8),
                Text(
                  'Advert Published!',
                  style: AppTheme.headlineMd.copyWith(color: AppTheme.onSurface),
                ),
              ],
            ),
            content: Text(
              'Your advertisement has been published to Big Live Marketplace.',
              style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
            ),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  _titleController.clear();
                  _priceController.clear();
                  _descController.clear();
                  setState(() => _selectedImages.clear());
                },
                child: Text(
                  'DONE',
                  style: AppTheme.labelMd.copyWith(color: AppTheme.primaryLavender),
                ),
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
            content: Text(
              'Advert saved offline: $e',
              style: const TextStyle(color: Colors.white),
            ),
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
                      'CREATE LISTING',
                      style: AppTheme.labelUppercase,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Post an Advert',
                      style: AppTheme.displayLg,
                    ),
                  ],
                ),
              ),

              // Scrollable Form
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.fromLTRB(
                    AppTheme.gutter,
                    8,
                    AppTheme.gutter,
                    120, // Clear floating bottom tab bar
                  ),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        // Photo Upload / Preview Slot
                        if (_selectedImages.isEmpty)
                          GestureDetector(
                            onTap: _pickImages,
                            child: Container(
                              height: 120,
                              decoration: BoxDecoration(
                                color: AppTheme.surfaceGlass,
                                borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
                                border: Border.all(
                                  color: AppTheme.borderHairline,
                                  width: 1.5,
                                ),
                              ),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const TintedIconBubble(
                                    icon: Icons.add_a_photo_rounded,
                                    accentColor: AppTheme.primaryLavender,
                                    size: 48,
                                    iconSize: 24,
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    'ADD PHOTOS & MEDIA',
                                    style: AppTheme.labelUppercase.copyWith(
                                      color: AppTheme.primaryLavender,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          )
                        else
                          SizedBox(
                            height: 110,
                            child: ListView.separated(
                              scrollDirection: Axis.horizontal,
                              itemCount: _selectedImages.length + 1,
                              separatorBuilder: (_, __) => const SizedBox(width: 10),
                              itemBuilder: (ctx, index) {
                                if (index == _selectedImages.length) {
                                  // Add more button
                                  return GestureDetector(
                                    onTap: _pickImages,
                                    child: Container(
                                      width: 90,
                                      decoration: BoxDecoration(
                                        color: AppTheme.surfaceGlass,
                                        borderRadius: BorderRadius.circular(AppTheme.radiusLg),
                                        border: Border.all(color: AppTheme.borderHairline),
                                      ),
                                      child: const Column(
                                        mainAxisAlignment: MainAxisAlignment.center,
                                        children: [
                                          Icon(Icons.add_photo_alternate_rounded, color: AppTheme.primaryLavender, size: 26),
                                          SizedBox(height: 4),
                                          Text('Add More', style: TextStyle(color: AppTheme.primaryLavender, fontSize: 11)),
                                        ],
                                      ),
                                    ),
                                  );
                                }

                                final img = _selectedImages[index];
                                return Stack(
                                  children: [
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(AppTheme.radiusLg),
                                      child: Image.file(
                                        File(img.path),
                                        width: 100,
                                        height: 110,
                                        fit: BoxFit.cover,
                                      ),
                                    ),
                                    Positioned(
                                      top: 4,
                                      right: 4,
                                      child: GestureDetector(
                                        onTap: () => setState(() => _selectedImages.removeAt(index)),
                                        child: Container(
                                          padding: const EdgeInsets.all(4),
                                          decoration: BoxDecoration(
                                            color: AppTheme.backgroundDark.withValues(alpha: 0.8),
                                            shape: BoxShape.circle,
                                          ),
                                          child: const Icon(
                                            Icons.close_rounded,
                                            size: 14,
                                            color: AppTheme.accentPink,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ],
                                );
                              },
                            ),
                          ),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Title Input
                        _buildInputField(
                          label: 'ITEM TITLE',
                          controller: _titleController,
                          hint: 'e.g. iPhone 15 Pro Max, Toyota Prado',
                          validator: (v) => (v == null || v.trim().isEmpty) ? 'Please enter a title' : null,
                        ),

                        const SizedBox(height: 16),

                        // Price Input
                        _buildInputField(
                          label: 'PRICE (PKR)',
                          controller: _priceController,
                          hint: '50000',
                          keyboardType: TextInputType.number,
                          suffix: 'PKR',
                          validator: (v) => (v == null || v.trim().isEmpty) ? 'Please enter price' : null,
                        ),

                        const SizedBox(height: 16),

                        // Category Chips
                        Text(
                          'CATEGORY',
                          style: AppTheme.labelUppercase,
                        ),
                        const SizedBox(height: 8),
                        SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: Row(
                            children: _categories.map((c) {
                              final isSelected = _selectedCategory == c['id'];
                              return Padding(
                                padding: const EdgeInsets.only(right: 8),
                                child: GestureDetector(
                                  onTap: () => setState(() => _selectedCategory = c['id'] as int),
                                  child: AnimatedContainer(
                                    duration: const Duration(milliseconds: 200),
                                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                    decoration: BoxDecoration(
                                      color: isSelected
                                          ? AppTheme.primaryLavender.withValues(alpha: 0.20)
                                          : AppTheme.surfaceGlass,
                                      borderRadius: BorderRadius.circular(AppTheme.radiusSm),
                                      border: Border.all(
                                        color: isSelected
                                            ? AppTheme.primaryLavender
                                            : AppTheme.borderHairline,
                                      ),
                                    ),
                                    child: Text(
                                      c['name'] as String,
                                      style: AppTheme.bodyMd.copyWith(
                                        color: isSelected ? AppTheme.primaryLavender : AppTheme.onSurfaceMuted,
                                        fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                                      ),
                                    ),
                                  ),
                                ),
                              );
                            }).toList(),
                          ),
                        ),

                        const SizedBox(height: 16),

                        // Condition Segmented Control
                        Text(
                          'CONDITION',
                          style: AppTheme.labelUppercase,
                        ),
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            color: AppTheme.surfaceGlass,
                            borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                            border: Border.all(color: AppTheme.borderHairline),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: GestureDetector(
                                  onTap: () => setState(() => _selectedCondition = 'used'),
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(vertical: 10),
                                    decoration: BoxDecoration(
                                      color: _selectedCondition == 'used'
                                          ? Colors.white.withValues(alpha: 0.10)
                                          : Colors.transparent,
                                      borderRadius: BorderRadius.circular(AppTheme.radiusSm),
                                    ),
                                    alignment: Alignment.center,
                                    child: Text(
                                      'USED / PRE-OWNED',
                                      style: AppTheme.labelUppercase.copyWith(
                                        color: _selectedCondition == 'used'
                                            ? AppTheme.onSurface
                                            : AppTheme.onSurfaceMuted,
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                              Expanded(
                                child: GestureDetector(
                                  onTap: () => setState(() => _selectedCondition = 'new'),
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(vertical: 10),
                                    decoration: BoxDecoration(
                                      color: _selectedCondition == 'new'
                                          ? Colors.white.withValues(alpha: 0.10)
                                          : Colors.transparent,
                                      borderRadius: BorderRadius.circular(AppTheme.radiusSm),
                                    ),
                                    alignment: Alignment.center,
                                    child: Text(
                                      'BRAND NEW',
                                      style: AppTheme.labelUppercase.copyWith(
                                        color: _selectedCondition == 'new'
                                            ? AppTheme.secondaryMint
                                            : AppTheme.onSurfaceMuted,
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: 16),

                        // Location Input
                        _buildInputField(
                          label: 'LOCATION',
                          controller: _locationController,
                          hint: 'e.g. DHA Phase 6, Karachi',
                          validator: (v) => (v == null || v.trim().isEmpty) ? 'Please enter location' : null,
                        ),

                        const SizedBox(height: 16),

                        // Description Input
                        _buildInputField(
                          label: 'DESCRIPTION',
                          controller: _descController,
                          hint: 'Provide details on warranty, condition, features...',
                          maxLines: 4,
                          validator: (v) => (v == null || v.trim().isEmpty) ? 'Please enter description' : null,
                        ),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Submit CTA
                        NebulaButtonPrimary(
                          label: 'Publish Advert',
                          isLoading: _isSubmitting,
                          onPressed: _submitAd,
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
    );
  }

  Widget _buildInputField({
    required String label,
    required TextEditingController controller,
    required String hint,
    int maxLines = 1,
    TextInputType keyboardType = TextInputType.text,
    String? suffix,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: AppTheme.labelUppercase,
        ),
        const SizedBox(height: 6),
        Container(
          decoration: BoxDecoration(
            color: AppTheme.surfaceGlass,
            borderRadius: BorderRadius.circular(AppTheme.radiusMd),
            border: Border.all(color: AppTheme.borderHairline),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
          child: TextFormField(
            controller: controller,
            maxLines: maxLines,
            keyboardType: keyboardType,
            style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
            validator: validator,
            decoration: InputDecoration(
              hintText: hint,
              hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
              border: InputBorder.none,
              suffixText: suffix,
              suffixStyle: AppTheme.labelUppercase.copyWith(color: AppTheme.primaryLavender),
            ),
          ),
        ),
      ],
    );
  }
}
