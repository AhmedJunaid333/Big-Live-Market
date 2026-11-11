import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';

final devicesProvider = FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.dio.get('/me/devices');
  final data = response.data['data'];
  if (data is List) return data;
  return [];
});

class DevicesScreen extends ConsumerWidget {
  const DevicesScreen({super.key});

  Future<void> _revokeDevice(BuildContext context, WidgetRef ref, int deviceId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: AppTheme.backgroundMid,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppTheme.radiusXl),
          side: const BorderSide(color: AppTheme.borderHairline),
        ),
        title: Text('Revoke Session?', style: AppTheme.headlineMd),
        content: Text(
          'This will log out the selected device immediately.',
          style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: Text('CANCEL', style: AppTheme.labelMd.copyWith(color: AppTheme.onSurfaceVariant)),
          ),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: Text('REVOKE', style: AppTheme.labelMd.copyWith(color: AppTheme.accentPink)),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    try {
      final client = ref.read(apiClientProvider);
      await client.dio.delete('/me/devices/$deviceId');
      ref.invalidate(devicesProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: AppTheme.secondaryMint,
            content: Text('Device session revoked'),
          ),
        );
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.error,
            content: Text('Failed to revoke session: $e'),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final devicesAsync = ref.watch(devicesProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Top Bar
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 12),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_ios_new_rounded,
                      onPressed: () => Navigator.pop(context),
                      tooltip: 'Back',
                    ),
                    const SizedBox(width: 14),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('SECURITY & AUDIT', style: AppTheme.labelUppercase),
                        Text('Active Sessions', style: AppTheme.headlineMd),
                      ],
                    ),
                    const Spacer(),
                    NebulaGlassIconButton(
                      icon: Icons.refresh_rounded,
                      onPressed: () => ref.invalidate(devicesProvider),
                      tooltip: 'Refresh',
                    ),
                  ],
                ),
              ),

              // Device List
              Expanded(
                child: devicesAsync.when(
                  data: (devices) {
                    if (devices.isEmpty) {
                      return Center(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const TintedIconBubble(
                              icon: Icons.devices_rounded,
                              tint: AppTheme.accentCyan,
                              size: 56,
                            ),
                            const SizedBox(height: 16),
                            Text('No active device sessions found', style: AppTheme.headlineMd),
                            const SizedBox(height: 8),
                            Text(
                              'When you log in on mobile or web, your active sessions appear here.',
                              style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),
                      );
                    }

                    return ListView.separated(
                      padding: const EdgeInsets.all(AppTheme.gutter),
                      itemCount: devices.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 12),
                      itemBuilder: (ctx, index) {
                        final d = devices[index] as Map<String, dynamic>;
                        final id = (d['id'] as num?)?.toInt() ?? 0;
                        final name = d['device_name']?.toString() ?? 'Mobile Client';
                        final ip = d['ip_address']?.toString() ?? 'Unknown IP';
                        final isRevoked = d['revoked_at'] != null;
                        final lastSeen = d['last_seen_at']?.toString() ?? 'Recently';

                        return NebulaGlassCard(
                          child: Row(
                            children: [
                              TintedIconBubble(
                                icon: name.toLowerCase().contains('phone') || name.toLowerCase().contains('android')
                                    ? Icons.phone_android_rounded
                                    : Icons.laptop_mac_rounded,
                                tint: isRevoked ? AppTheme.onSurfaceMuted : AppTheme.secondaryMint,
                              ),
                              const SizedBox(width: 14),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Text(name, style: AppTheme.headlineMd),
                                        const SizedBox(width: 8),
                                        if (index == 0 && !isRevoked)
                                          const NebulaBadgePill(
                                            text: 'CURRENT',
                                            tone: NebulaBadgeTone.mint,
                                          )
                                        else if (isRevoked)
                                          const NebulaBadgePill(
                                            text: 'REVOKED',
                                            tone: NebulaBadgeTone.pink,
                                          ),
                                      ],
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      'IP: $ip • Last seen: $lastSeen',
                                      style: AppTheme.bodySm.copyWith(
                                        color: AppTheme.onSurfaceVariant,
                                        fontSize: 11,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              if (!isRevoked)
                                IconButton(
                                  icon: const Icon(
                                    Icons.logout_rounded,
                                    color: AppTheme.accentPink,
                                    size: 20,
                                  ),
                                  onPressed: () => _revokeDevice(context, ref, id),
                                  tooltip: 'Revoke',
                                ),
                            ],
                          ),
                        );
                      },
                    );
                  },
                  loading: () => const Center(
                    child: CircularProgressIndicator(color: AppTheme.primaryLavender),
                  ),
                  error: (err, _) => Center(
                    child: Padding(
                      padding: const EdgeInsets.all(AppTheme.gutter),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            'Please sign in to inspect active sessions',
                            style: AppTheme.headlineMd,
                            textAlign: TextAlign.center,
                          ),
                          const SizedBox(height: 8),
                          Text(
                            '$err',
                            style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
                            textAlign: TextAlign.center,
                          ),
                        ],
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
