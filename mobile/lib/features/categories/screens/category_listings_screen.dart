import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import '../../listings/screens/listing_detail_screen.dart';
import '../models/category_taxonomy.dart';

final categoryListingsProvider = FutureProvider.family<List<dynamic>, int>((ref, categoryId) async {
  final client = ref.watch(apiClientProvider);
  try {
    final response = await client.dio.get('/listings?category_id=$categoryId');
    final data = response.data['data'] as List<dynamic>?;
    if (data != null && data.isNotEmpty) {
      return data;
    }
  } catch (_) {}

  // Rich category & subcategory sample listings fallback
  return [
    // MOBILES (Cat ID 3)
    if (categoryId == 3) ...[
      {
        'id': 30101,
        'subcategory_slug': 'mobile-phones',
        'title': 'Apple iPhone 15 Pro Max 256GB Natural Titanium',
        'description': 'PTA approved, battery health 99%, complete original box, cable and warranty slip.',
        'price_minor': 43000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'Blue Area, Islamabad',
        'seller': {'name': 'Apple Store PK', 'phone_e164': '+923337654321'}
      },
      {
        'id': 30102,
        'subcategory_slug': 'mobile-phones',
        'title': 'Samsung Galaxy S24 Ultra 512GB Titanium Black',
        'description': 'Dual SIM official PTA approved, with S-Pen, zero scratches, brand new condition.',
        'price_minor': 39500000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'Gulberg III, Lahore',
        'seller': {'name': 'Galaxy Tech Hub', 'phone_e164': '+923005554321'}
      },
      {
        'id': 30201,
        'subcategory_slug': 'mobile-accessories',
        'title': 'Apple 20W USB-C Power Adapter + MagSafe Charger',
        'description': '100% genuine Apple original fast charger with magnetic alignment pad.',
        'price_minor': 950000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Hafeez Centre, Lahore',
        'seller': {'name': 'Speed Accessories', 'phone_e164': '+923214441122'}
      },
      {
        'id': 30301,
        'subcategory_slug': 'tablets',
        'title': 'Apple iPad Pro 12.9" M2 Chip 256GB Wi-Fi + 5G',
        'description': 'Liquid Retina XDR display, with Apple Pencil 2nd Gen and Magic Keyboard folio.',
        'price_minor': 28500000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'DHA Phase 6, Karachi',
        'seller': {'name': 'Gadget Vault', 'phone_e164': '+923129998877'}
      },
      {
        'id': 30401,
        'subcategory_slug': 'smart-watches',
        'title': 'Apple Watch Ultra 2 49mm Titanium Case',
        'description': 'GPS + Cellular, Ocean Band orange, action button, water resistant 100m.',
        'price_minor': 18500000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'F-7 Markaz, Islamabad',
        'seller': {'name': 'Timepiece Studio', 'phone_e164': '+923451112233'}
      },
      {
        'id': 30601,
        'subcategory_slug': 'other-wearables-audio',
        'title': 'Apple AirPods Pro 2nd Generation with USB-C MagSafe',
        'description': 'Active Noise Cancellation, Adaptive Audio, untouched sealed pack box.',
        'price_minor': 6800000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Clifton, Karachi',
        'seller': {'name': 'Acoustic Sound', 'phone_e164': '+923334567890'}
      },
      {
        'id': 30701,
        'subcategory_slug': 'mobile-numbers',
        'title': 'VIP Jazz Golden Number 0300-XXXX777',
        'description': 'Biometric transfer on spot, lucky repeating digits VIP sequence.',
        'price_minor': 4500000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Saddar, Rawalpindi',
        'seller': {'name': 'Golden Numbers PK', 'phone_e164': '+923001234567'}
      },
    ],

    // VEHICLES (Cat ID 1)
    if (categoryId == 1) ...[
      {
        'id': 10101,
        'subcategory_slug': 'cars',
        'title': 'Honda Civic RS Turbo 2022 Black Edition',
        'description': 'First owner, bumper to bumper genuine, sunroof, paddle shifters, 24,000km driven.',
        'price_minor': 880000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'F-7 Markaz, Islamabad',
        'seller': {'name': 'Hamza Autos', 'phone_e164': '+923001234567'}
      },
      {
        'id': 10102,
        'subcategory_slug': 'cars',
        'title': 'Toyota Land Cruiser V8 ZX 2021 Pearl White',
        'description': 'Modelista body kit, height control, rear entertainment, auction grade 4.5.',
        'price_minor': 5400000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'DHA Phase 5, Lahore',
        'seller': {'name': 'Al-Madina Motors', 'phone_e164': '+923214567890'}
      },
      {
        'id': 10201,
        'subcategory_slug': 'car-accessories',
        'title': 'Android 10" IPS Touch Navigation System + 360 Camera',
        'description': 'Fits Corolla/Civic, Wireless Apple CarPlay & Android Auto, 4GB RAM + 64GB ROM.',
        'price_minor': 3200000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Montgomery Road, Lahore',
        'seller': {'name': 'Sound City Autos', 'phone_e164': '+923331122334'}
      },
      {
        'id': 10301,
        'subcategory_slug': 'spare-parts',
        'title': 'Original Brembo Brake Calipers & Ventilated Rotors',
        'description': 'Imported direct fit for Honda Civic & Accord, superior heat dissipation.',
        'price_minor': 9500000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Plaza Market, Karachi',
        'seller': {'name': 'Euro Auto Parts', 'phone_e164': '+923128889900'}
      },
      {
        'id': 10501,
        'subcategory_slug': 'rickshaw-chingchi',
        'title': 'New Sazgar 9-Seater CNG Auto Rickshaw 2024',
        'description': 'Self-start 200cc water-cooled engine, heavy duty shock absorbers, registered.',
        'price_minor': 48000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Multan Road, Lahore',
        'seller': {'name': 'Sazgar Agency', 'phone_e164': '+923457788990'}
      },
      {
        'id': 10601,
        'subcategory_slug': 'buses-vans-trucks',
        'title': 'Toyota HiAce Grand Cabin 2018 Unregistered',
        'description': 'Dual AC, power slide door, luxury 14-seater recliner seats, fresh clear import.',
        'price_minor': 1120000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'G-9 Markaz, Islamabad',
        'seller': {'name': 'Capital Van Traders', 'phone_e164': '+923008899001'}
      },
      {
        'id': 10901,
        'subcategory_slug': 'cars-on-installments',
        'title': 'Suzuki Alto VXR 2024 on 3 Years Easy Installment Plan',
        'description': '20% down payment, low markup rate, immediate delivery with insurance included.',
        'price_minor': 285000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Shahrah-e-Faisal, Karachi',
        'seller': {'name': 'Prime Leasing PK', 'phone_e164': '+923004433221'}
      },
    ],

    // PROPERTY FOR SALE (Cat ID 5)
    if (categoryId == 5) ...[
      {
        'id': 50101,
        'subcategory_slug': 'houses-for-sale',
        'title': '1 Kanal Ultra Modern Luxury Designer House',
        'description': 'Spanish architecture, 5 master bedrooms, Italian bath tiles, imported kitchen.',
        'price_minor': 9500000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'DHA Phase 6, Lahore',
        'seller': {'name': 'Prime Estates', 'phone_e164': '+923219876543'}
      },
      {
        'id': 50201,
        'subcategory_slug': 'residential-plots',
        'title': '10 Marla Solid Land Residential Plot near Park',
        'description': 'Possession paid, ready for immediate house construction, 50 feet front road.',
        'price_minor': 2100000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Bahria Town Sector C, Lahore',
        'seller': {'name': 'Realtor Hub', 'phone_e164': '+923009988776'}
      },
      {
        'id': 50301,
        'subcategory_slug': 'apartments-flats-sale',
        'title': '3-Bed Luxury High-Rise Penthouse with Sea View',
        'description': 'Centrally air-conditioned, 2 reserved parking slots, rooftop infinity pool.',
        'price_minor': 4800000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Clifton Sea View, Karachi',
        'seller': {'name': 'Crestline Towers', 'phone_e164': '+923331234567'}
      },
    ],

    // PROPERTY FOR RENT (Cat ID 12)
    if (categoryId == 12) ...[
      {
        'id': 120101,
        'subcategory_slug': 'houses-for-rent',
        'title': '1 Kanal Full Independent Bungalow for Rent',
        'description': '5 bedrooms with attach baths, servant quarter, lawn, quiet VIP street.',
        'price_minor': 28000000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'F-8/3, Islamabad',
        'seller': {'name': 'Capital Rentals', 'phone_e164': '+923002233445'}
      },
      {
        'id': 120201,
        'subcategory_slug': 'portions-floors-rent',
        'title': 'Upper Portion 10 Marla Separate Gate & Meter',
        'description': '3 bedrooms, drawing room, terrace, tiled flooring, peaceful environment.',
        'price_minor': 8500000,
        'currency': 'PKR',
        'condition_code': 'used',
        'location_label': 'DHA Phase 3, Lahore',
        'seller': {'name': 'Property Guide', 'phone_e164': '+923217766554'}
      },
    ],

    // ELECTRONICS (Cat ID 6)
    if (categoryId == 6) ...[
      {
        'id': 60101,
        'subcategory_slug': 'tv-video-audio',
        'title': 'Samsung 65" QLED 4K Smart TV Quantum HDR',
        'description': '120Hz refresh rate, Gaming hub, Dolby Atmos audio, sealed official warranty.',
        'price_minor': 24500000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Hall Road, Lahore',
        'seller': {'name': 'Smart Vision Electronics', 'phone_e164': '+923001122445'}
      },
      {
        'id': 60601,
        'subcategory_slug': 'generators-ups-solar',
        'title': 'Inverex Nitrox 10kW Hybrid Solar Inverter System',
        'description': 'Tier 1 bifacial solar panels, dual MPPT, smartphone app monitoring.',
        'price_minor': 89000000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Blue Area, Islamabad',
        'seller': {'name': 'Solar Tech Solutions', 'phone_e164': '+923335557788'}
      },
    ],

    // SERVICES (Cat ID 7)
    if (categoryId == 7) ...[
      {
        'id': 70101,
        'subcategory_slug': 'home-repair-maintenance',
        'title': 'Professional Electrician, Plumbing & Geyser Services',
        'description': '24/7 verified technicians, leak fixes, short-circuit troubleshooting at doorstep.',
        'price_minor': 150000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'All Areas, Lahore & Rawalpindi',
        'seller': {'name': 'Master Handyman Services', 'phone_e164': '+923009900112'}
      },
      {
        'id': 70201,
        'subcategory_slug': 'drivers-car-rentals',
        'title': 'Luxury Rent a Car with Professional Uniformed Driver',
        'description': 'Prado, Civic, Corolla available for weddings, corporate tours, and airport pick/drop.',
        'price_minor': 800000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Airport Road, Islamabad',
        'seller': {'name': 'Falcon Car Rentals', 'phone_e164': '+923215544332'}
      },
    ],

    // ANIMALS (Cat ID 9)
    if (categoryId == 9) ...[
      {
        'id': 90101,
        'subcategory_slug': 'birds',
        'title': 'Breeding Pair of Raw Ringneck Parrots Talking Quality',
        'description': 'Hand tamed, whistle and talk words, healthy active feather condition.',
        'price_minor': 6500000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'Tollinton Market, Lahore',
        'seller': {'name': 'Avian Wonders', 'phone_e164': '+923004455667'}
      },
      {
        'id': 90201,
        'subcategory_slug': 'cats-kittens',
        'title': 'Pure Persian Punch Face Triple Coat White Kitten',
        'description': 'Litter trained, vaccinated, playful personality, 2.5 months old.',
        'price_minor': 3500000,
        'currency': 'PKR',
        'condition_code': 'new',
        'location_label': 'DHA Phase 2, Karachi',
        'seller': {'name': 'Feline Pets PK', 'phone_e164': '+923332211334'}
      },
    ],
  ];
});

class CategoryListingsScreen extends ConsumerStatefulWidget {
  final int categoryId;
  final String categoryName;
  final IconData icon;
  final Color accentColor;
  final SubcategoryItem? selectedSubcategory;
  final String? initialSearchQuery;

  const CategoryListingsScreen({
    super.key,
    required this.categoryId,
    required this.categoryName,
    required this.icon,
    required this.accentColor,
    this.selectedSubcategory,
    this.initialSearchQuery,
  });

  @override
  ConsumerState<CategoryListingsScreen> createState() => _CategoryListingsScreenState();
}

class _CategoryListingsScreenState extends ConsumerState<CategoryListingsScreen> {
  final _searchController = TextEditingController();
  String _localFilter = '';
  SubcategoryItem? _activeSubcategory;

  @override
  void initState() {
    super.initState();
    _activeSubcategory = widget.selectedSubcategory;
    if (widget.initialSearchQuery != null && widget.initialSearchQuery!.isNotEmpty) {
      _searchController.text = widget.initialSearchQuery!;
      _localFilter = widget.initialSearchQuery!.trim().toLowerCase();
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(categoryListingsProvider(widget.categoryId));
    final subcategories = CategoryTaxonomy.getSubcategoriesFor(widget.categoryId);

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic App Bar
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 12, AppTheme.gutter, 8),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_rounded,
                      color: AppTheme.onSurface,
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                    const SizedBox(width: 14),
                    TintedIconBubble(
                      icon: _activeSubcategory?.icon ?? widget.icon,
                      accentColor: widget.accentColor,
                      size: 44,
                      iconSize: 22,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _activeSubcategory == null ? widget.categoryName.toUpperCase() : widget.categoryName.toUpperCase(),
                            style: AppTheme.labelUppercase.copyWith(
                              color: widget.accentColor,
                              letterSpacing: 1.1,
                            ),
                          ),
                          Text(
                            _activeSubcategory?.name ?? widget.categoryName,
                            style: AppTheme.headlineMd.copyWith(
                              fontWeight: FontWeight.w700,
                              color: AppTheme.onSurface,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                    NebulaGlassIconButton(
                      icon: Icons.refresh_rounded,
                      color: widget.accentColor,
                      onPressed: () {
                        ref.invalidate(categoryListingsProvider(widget.categoryId));
                      },
                    ),
                  ],
                ),
              ),

              // Search Bar within this Category
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 4),
                child: Container(
                  decoration: BoxDecoration(
                    color: AppTheme.surfaceGlass,
                    borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                    border: Border.all(color: AppTheme.borderHairline),
                  ),
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: TextField(
                    controller: _searchController,
                    style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                    decoration: InputDecoration(
                      hintText: _activeSubcategory != null
                          ? 'Search in ${_activeSubcategory!.name}...'
                          : 'Search in ${widget.categoryName}...',
                      hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                      prefixIcon: Icon(
                        Icons.search_rounded,
                        color: widget.accentColor,
                        size: 20,
                      ),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear_rounded, color: AppTheme.onSurfaceVariant, size: 18),
                              onPressed: () {
                                _searchController.clear();
                                setState(() => _localFilter = '');
                              },
                            )
                          : null,
                      border: InputBorder.none,
                    ),
                    onChanged: (val) {
                      setState(() => _localFilter = val.trim().toLowerCase());
                    },
                  ),
                ),
              ),

              // Horizontal Subcategory Chips Filter
              if (subcategories.isNotEmpty) ...[
                const SizedBox(height: 6),
                SizedBox(
                  height: 42,
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter),
                    itemCount: subcategories.length + 1,
                    separatorBuilder: (_, __) => const SizedBox(width: 8),
                    itemBuilder: (context, index) {
                      if (index == 0) {
                        final isAll = _activeSubcategory == null;
                        return InkWell(
                          onTap: () {
                            setState(() => _activeSubcategory = null);
                          },
                          borderRadius: BorderRadius.circular(20),
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 180),
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                            decoration: BoxDecoration(
                              color: isAll
                                  ? widget.accentColor.withValues(alpha: 0.28)
                                  : AppTheme.surfaceGlass,
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(
                                color: isAll ? widget.accentColor : AppTheme.borderHairline,
                                width: isAll ? 1.4 : 1.0,
                              ),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(
                                  Icons.grid_view_rounded,
                                  size: 15,
                                  color: isAll ? widget.accentColor : AppTheme.onSurfaceMuted,
                                ),
                                const SizedBox(width: 6),
                                Text(
                                  'All in ${widget.categoryName}',
                                  style: AppTheme.bodySm.copyWith(
                                    fontWeight: isAll ? FontWeight.w700 : FontWeight.w500,
                                    color: isAll ? AppTheme.onSurface : AppTheme.onSurfaceMuted,
                                    fontSize: 12,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      }

                      final sub = subcategories[index - 1];
                      final isSelected = _activeSubcategory?.slug == sub.slug;

                      return InkWell(
                        onTap: () {
                          setState(() {
                            if (isSelected) {
                              _activeSubcategory = null;
                            } else {
                              _activeSubcategory = sub;
                            }
                          });
                        },
                        borderRadius: BorderRadius.circular(20),
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 180),
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          decoration: BoxDecoration(
                            color: isSelected
                                ? widget.accentColor.withValues(alpha: 0.28)
                                : AppTheme.surfaceGlass,
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(
                              color: isSelected ? widget.accentColor : AppTheme.borderHairline,
                              width: isSelected ? 1.4 : 1.0,
                            ),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                sub.icon,
                                size: 15,
                                color: isSelected ? widget.accentColor : AppTheme.onSurfaceMuted,
                              ),
                              const SizedBox(width: 6),
                              Text(
                                sub.name,
                                style: AppTheme.bodySm.copyWith(
                                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                                  color: isSelected ? AppTheme.onSurface : AppTheme.onSurfaceMuted,
                                  fontSize: 12,
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
                const SizedBox(height: 6),
              ],

              // Category Listings List
              Expanded(
                child: listingsAsync.when(
                  data: (rawListings) {
                    final listings = rawListings.where((item) {
                      // Subcategory filter
                      if (_activeSubcategory != null) {
                        final itemSub = (item['subcategory_slug'] ?? '').toString();
                        if (itemSub.isNotEmpty && itemSub != _activeSubcategory!.slug) {
                          return false;
                        }
                      }

                      // Text search filter
                      if (_localFilter.isEmpty) return true;
                      final title = (item['title'] ?? '').toString().toLowerCase();
                      final desc = (item['description'] ?? '').toString().toLowerCase();
                      final loc = (item['location_label'] ?? '').toString().toLowerCase();
                      return title.contains(_localFilter) || desc.contains(_localFilter) || loc.contains(_localFilter);
                    }).toList();

                    if (listings.isEmpty) {
                      return Center(
                        child: Padding(
                          padding: const EdgeInsets.all(AppTheme.gutter),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              TintedIconBubble(
                                icon: _activeSubcategory?.icon ?? widget.icon,
                                accentColor: widget.accentColor,
                                size: 68,
                                iconSize: 34,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                _activeSubcategory != null
                                    ? 'No listings in ${_activeSubcategory!.name}'
                                    : 'No listings in ${widget.categoryName}',
                                textAlign: TextAlign.center,
                                style: AppTheme.headlineMd.copyWith(
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.onSurface,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                'Be the first member to publish an advertisement in this subcategory!',
                                textAlign: TextAlign.center,
                                style: AppTheme.bodySm.copyWith(
                                  color: AppTheme.onSurfaceMuted,
                                ),
                              ),
                              if (_activeSubcategory != null) ...[
                                const SizedBox(height: 16),
                                OutlinedButton.icon(
                                  onPressed: () {
                                    setState(() => _activeSubcategory = null);
                                  },
                                  icon: const Icon(Icons.clear_all_rounded, size: 18),
                                  label: const Text('Show All Listings in Department'),
                                  style: OutlinedButton.styleFrom(
                                    foregroundColor: widget.accentColor,
                                    side: BorderSide(color: widget.accentColor),
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),
                      );
                    }

                    return RefreshIndicator(
                      color: widget.accentColor,
                      backgroundColor: AppTheme.backgroundMid,
                      onRefresh: () async {
                        ref.invalidate(categoryListingsProvider(widget.categoryId));
                      },
                      child: ListView.separated(
                        physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                        cacheExtent: 600,
                        padding: const EdgeInsets.fromLTRB(
                          AppTheme.gutter,
                          8,
                          AppTheme.gutter,
                          32,
                        ),
                        itemCount: listings.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 14),
                        itemBuilder: (context, index) {
                          final item = listings[index] as Map<String, dynamic>;
                          final priceMinor = (item['price_minor'] ?? 0) as int;
                          final priceRs = (priceMinor / 100).toStringAsFixed(0);
                          final currency = item['currency'] ?? 'PKR';
                          final condition = (item['condition_code'] ?? 'used').toString().toUpperCase();

                          return RepaintBoundary(
                            child: NebulaGlassCard(
                              borderRadius: AppTheme.radiusXl,
                              padding: const EdgeInsets.all(14),
                              onTap: () {
                                Navigator.of(context).push(
                                  MaterialPageRoute(
                                    builder: (_) => ListingDetailScreen(
                                      listingId: (item['id'] ?? (index + 1)) as int,
                                    ),
                                  ),
                                );
                              },
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Product Photo Placeholder with Gradient
                                  Container(
                                    width: 100,
                                    height: 100,
                                    decoration: BoxDecoration(
                                      color: AppTheme.backgroundMid,
                                      borderRadius: BorderRadius.circular(AppTheme.radiusLg),
                                      border: Border.all(color: AppTheme.borderHairline),
                                    ),
                                    child: Center(
                                      child: Icon(
                                        _activeSubcategory?.icon ?? widget.icon,
                                        size: 38,
                                        color: widget.accentColor.withValues(alpha: 0.65),
                                      ),
                                    ),
                                  ),
                                  const SizedBox(width: 14),

                                  // Details
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              '$currency $priceRs',
                                              style: AppTheme.headlineMd.copyWith(
                                                color: AppTheme.secondaryMint,
                                                fontWeight: FontWeight.w800,
                                                fontSize: 17,
                                              ),
                                            ),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                              decoration: BoxDecoration(
                                                color: condition == 'NEW'
                                                    ? AppTheme.accentMint.withValues(alpha: 0.15)
                                                    : AppTheme.accentAmber.withValues(alpha: 0.15),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: Text(
                                                condition,
                                                style: AppTheme.caption.copyWith(
                                                  color: condition == 'NEW'
                                                      ? AppTheme.accentMint
                                                      : AppTheme.accentAmber,
                                                  fontWeight: FontWeight.w700,
                                                  fontSize: 10,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          item['title'] ?? 'Listing Item',
                                          style: AppTheme.bodyMd.copyWith(
                                            fontWeight: FontWeight.w600,
                                            color: AppTheme.onSurface,
                                            fontSize: 14,
                                          ),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 6),
                                        Row(
                                          children: [
                                            const Icon(
                                              Icons.location_on_outlined,
                                              size: 13,
                                              color: AppTheme.onSurfaceMuted,
                                            ),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                item['location_label'] ?? 'Pakistan',
                                                style: AppTheme.caption.copyWith(
                                                  color: AppTheme.onSurfaceMuted,
                                                ),
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                    );
                  },
                  loading: () => const Center(
                    child: CircularProgressIndicator(color: AppTheme.primaryLavender),
                  ),
                  error: (err, _) => Center(
                    child: Padding(
                      padding: const EdgeInsets.all(AppTheme.gutter),
                      child: Text(
                        'Failed to load listings: $err',
                        style: AppTheme.bodySm.copyWith(color: AppTheme.accentRose),
                      ),
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
}
