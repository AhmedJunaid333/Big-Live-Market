import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../providers/filter_provider.dart';

class FilterBottomSheet extends ConsumerStatefulWidget {
  const FilterBottomSheet({super.key});

  static Future<void> show(BuildContext context) {
    return showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => const FilterBottomSheet(),
    );
  }

  @override
  ConsumerState<FilterBottomSheet> createState() => _FilterBottomSheetState();
}

class _FilterBottomSheetState extends ConsumerState<FilterBottomSheet> {
  late String _selectedSort;
  late String? _selectedCondition;
  late String? _selectedCity;
  late double? _selectedMinPrice;
  late double? _selectedMaxPrice;

  final List<String> _cities = const [
    'Lahore',
    'Karachi',
    'Islamabad',
    'Rawalpindi',
    'Faisalabad',
    'Multan',
  ];

  @override
  void initState() {
    super.initState();
    final current = ref.read(filterProvider);
    _selectedSort = current.sortBy;
    _selectedCondition = current.condition;
    _selectedCity = current.city;
    _selectedMinPrice = current.minPrice;
    _selectedMaxPrice = current.maxPrice;
  }

  void _setPricePreset(double? min, double? max) {
    setState(() {
      _selectedMinPrice = min;
      _selectedMaxPrice = max;
    });
  }

  void _apply() {
    final notifier = ref.read(filterProvider.notifier);
    notifier.setSortBy(_selectedSort);
    notifier.setCondition(_selectedCondition);
    notifier.setCity(_selectedCity);
    notifier.setMinPrice(_selectedMinPrice);
    notifier.setMaxPrice(_selectedMaxPrice);
    Navigator.pop(context);
  }

  void _reset() {
    setState(() {
      _selectedSort = 'newest';
      _selectedCondition = null;
      _selectedCity = null;
      _selectedMinPrice = null;
      _selectedMaxPrice = null;
    });
    ref.read(filterProvider.notifier).reset();
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: const BorderRadius.vertical(top: Radius.circular(AppTheme.radius2Xl)),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 24, sigmaY: 24),
        child: Container(
          decoration: BoxDecoration(
            color: AppTheme.backgroundMid.withValues(alpha: 0.92),
            borderRadius: const BorderRadius.vertical(top: Radius.circular(AppTheme.radius2Xl)),
            border: const Border(
              top: BorderSide(color: AppTheme.borderHairline, width: 1.2),
            ),
          ),
          padding: EdgeInsets.fromLTRB(
            AppTheme.gutter,
            16,
            AppTheme.gutter,
            MediaQuery.of(context).viewInsets.bottom + 28,
          ),
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Handle bar
                Center(
                  child: Container(
                    width: 44,
                    height: 4,
                    decoration: BoxDecoration(
                      color: AppTheme.onSurfaceVariant.withValues(alpha: 0.35),
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // Title row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('MARKETPLACE FILTERS', style: AppTheme.labelUppercase),
                    TextButton(
                      onPressed: _reset,
                      child: Text(
                        'Reset All',
                        style: AppTheme.labelSm.copyWith(color: AppTheme.accentCyan),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Sort Options
                Text('Sort Listings', style: AppTheme.headlineMd),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    _buildChoiceChip(
                      label: 'Newest First',
                      selected: _selectedSort == 'newest',
                      onTap: () => setState(() => _selectedSort = 'newest'),
                    ),
                    _buildChoiceChip(
                      label: 'Price: Low → High',
                      selected: _selectedSort == 'price_asc',
                      onTap: () => setState(() => _selectedSort = 'price_asc'),
                    ),
                    _buildChoiceChip(
                      label: 'Price: High → Low',
                      selected: _selectedSort == 'price_desc',
                      onTap: () => setState(() => _selectedSort = 'price_desc'),
                    ),
                  ],
                ),
                const SizedBox(height: 20),

                // Condition
                Text('Condition', style: AppTheme.headlineMd),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    _buildChoiceChip(
                      label: 'All Conditions',
                      selected: _selectedCondition == null,
                      onTap: () => setState(() => _selectedCondition = null),
                    ),
                    _buildChoiceChip(
                      label: 'Brand New',
                      selected: _selectedCondition == 'new',
                      onTap: () => setState(() => _selectedCondition = 'new'),
                    ),
                    _buildChoiceChip(
                      label: 'Used',
                      selected: _selectedCondition == 'used',
                      onTap: () => setState(() => _selectedCondition = 'used'),
                    ),
                    _buildChoiceChip(
                      label: 'Refurbished',
                      selected: _selectedCondition == 'refurbished',
                      onTap: () => setState(() => _selectedCondition = 'refurbished'),
                    ),
                  ],
                ),
                const SizedBox(height: 20),

                // Price Range
                Text('Price Budget (PKR)', style: AppTheme.headlineMd),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    _buildChoiceChip(
                      label: 'Any Budget',
                      selected: _selectedMinPrice == null && _selectedMaxPrice == null,
                      onTap: () => _setPricePreset(null, null),
                    ),
                    _buildChoiceChip(
                      label: '< Rs 50K',
                      selected: _selectedMinPrice == null && _selectedMaxPrice == 50000,
                      onTap: () => _setPricePreset(null, 50000),
                    ),
                    _buildChoiceChip(
                      label: 'Rs 50K – 200K',
                      selected: _selectedMinPrice == 50000 && _selectedMaxPrice == 200000,
                      onTap: () => _setPricePreset(50000, 200000),
                    ),
                    _buildChoiceChip(
                      label: 'Rs 200K – 1M',
                      selected: _selectedMinPrice == 200000 && _selectedMaxPrice == 1000000,
                      onTap: () => _setPricePreset(200000, 1000000),
                    ),
                    _buildChoiceChip(
                      label: '> Rs 1M',
                      selected: _selectedMinPrice == 1000000 && _selectedMaxPrice == null,
                      onTap: () => _setPricePreset(1000000, null),
                    ),
                  ],
                ),
                const SizedBox(height: 20),

                // Cities
                Text('City / Region', style: AppTheme.headlineMd),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    _buildChoiceChip(
                      label: 'All Pakistan',
                      selected: _selectedCity == null,
                      onTap: () => setState(() => _selectedCity = null),
                    ),
                    ..._cities.map((c) => _buildChoiceChip(
                          label: c,
                          selected: _selectedCity == c,
                          onTap: () => setState(() => _selectedCity = c),
                        )),
                  ],
                ),
                const SizedBox(height: 28),

                // Apply Button
                NebulaButtonPrimary(
                  label: 'APPLY FILTERS',
                  icon: Icons.check_circle_outline,
                  onPressed: _apply,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildChoiceChip({
    required String label,
    required bool selected,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(AppTheme.radiusFull),
          color: selected
              ? AppTheme.primaryLavender.withValues(alpha: 0.25)
              : AppTheme.glassSurface,
          border: Border.all(
            color: selected ? AppTheme.primaryLavender : AppTheme.borderHairline,
            width: selected ? 1.5 : 1,
          ),
        ),
        child: Text(
          label,
          style: AppTheme.labelSm.copyWith(
            color: selected ? AppTheme.primaryLavender : AppTheme.onSurfaceVariant,
            fontWeight: selected ? FontWeight.w700 : FontWeight.w500,
          ),
        ),
      ),
    );
  }
}
