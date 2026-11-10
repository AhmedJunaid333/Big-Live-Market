import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
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

  @override
  void dispose() {
    _titleController.dispose();
    _priceController.dispose();
    _locationController.dispose();
    _descController.dispose();
    super.dispose();
  }

  Future<void> _submitAd() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);

    try {
      final client = ref.read(apiClientProvider);
      final price = double.tryParse(_priceController.text) ?? 50000;

      await client.dio.post(
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

      ref.invalidate(listingsFeedProvider);

      if (mounted) {
        showDialog(
          context: context,
          builder: (ctx) => AlertDialog(
            title: const Row(
              children: [
                Icon(Icons.check_circle, color: AppTheme.successGreen),
                SizedBox(width: 8),
                Text('Advert Published!'),
              ],
            ),
            content: const Text('Your advertisement has been published to Big Live Marketplace.'),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  _titleController.clear();
                  _priceController.clear();
                  _descController.clear();
                },
                child: const Text('Done'),
              ),
            ],
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Note: ${e.toString()} (Feed refreshed)')),
        );
        ref.invalidate(listingsFeedProvider);
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
      appBar: AppBar(
        title: const Text('Post An Advert'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(AppTheme.space16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Container(
                padding: const EdgeInsets.all(AppTheme.space16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(AppTheme.radiusMedium),
                  border: Border.all(color: AppTheme.borderSubtle),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Item Details', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.obsidian)),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _titleController,
                      decoration: const InputDecoration(
                        labelText: 'Ad Title *',
                        hintText: 'e.g. Toyota Prado TX 2021 Mint Condition',
                      ),
                      validator: (val) => val == null || val.length < 5 ? 'Please enter a descriptive title' : null,
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<int>(
                      value: _selectedCategory,
                      decoration: const InputDecoration(labelText: 'Category *'),
                      items: const [
                        DropdownMenuItem(value: 1, child: Text('Cars & Motors')),
                        DropdownMenuItem(value: 2, child: Text('Mobile Phones')),
                        DropdownMenuItem(value: 3, child: Text('Real Estate')),
                        DropdownMenuItem(value: 4, child: Text('Electronics')),
                      ],
                      onChanged: (val) => setState(() => _selectedCategory = val ?? 1),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: TextFormField(
                            controller: _priceController,
                            decoration: const InputDecoration(labelText: 'Price (PKR) *', prefixText: 'Rs. '),
                            keyboardType: TextInputType.number,
                            validator: (val) => val == null || val.isEmpty ? 'Required' : null,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: DropdownButtonFormField<String>(
                            value: _selectedCondition,
                            decoration: const InputDecoration(labelText: 'Condition *'),
                            items: const [
                              DropdownMenuItem(value: 'used', child: Text('Used')),
                              DropdownMenuItem(value: 'new', child: Text('Brand New')),
                              DropdownMenuItem(value: 'refurbished', child: Text('Refurbished')),
                            ],
                            onChanged: (val) => setState(() => _selectedCondition = val ?? 'used'),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _locationController,
                      decoration: const InputDecoration(labelText: 'Location / City *'),
                      validator: (val) => val == null || val.isEmpty ? 'Required' : null,
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _descController,
                      decoration: const InputDecoration(
                        labelText: 'Full Description *',
                        hintText: 'Provide condition, package contents, warranty details...',
                      ),
                      maxLines: 4,
                      validator: (val) => val == null || val.length < 10 ? 'At least 10 characters required' : null,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: _isSubmitting ? null : _submitAd,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.champagne,
                  foregroundColor: AppTheme.obsidian,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: _isSubmitting
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.obsidian))
                    : const Text('PUBLISH ADVERT NOW', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 0.5)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
