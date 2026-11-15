import 'package:flutter/material.dart';
import '../models/earning_models.dart';

class SponsoredMissionsSheet extends StatefulWidget {
  final Function(SponsoredMission mission)? onMissionCompleted;

  const SponsoredMissionsSheet({super.key, this.onMissionCompleted});

  @override
  State<SponsoredMissionsSheet> createState() => _SponsoredMissionsSheetState();
}

class _SponsoredMissionsSheetState extends State<SponsoredMissionsSheet> {
  final List<SponsoredMission> _missions = [
    const SponsoredMission(
      id: 1,
      title: 'Watch Toyota Partner Promo (30s)',
      description: 'View full 30-second sponsored video and explore features',
      rewardPkr: 50,
      actionLabel: 'Watch & Earn Rs. 50',
      iconType: 'video',
    ),
    const SponsoredMission(
      id: 2,
      title: 'DHA Properties Quick Survey',
      description: 'Answer 3 quick questions about real estate preferences',
      rewardPkr: 100,
      actionLabel: 'Start Survey (Rs. 100)',
      iconType: 'survey',
    ),
    const SponsoredMission(
      id: 3,
      title: 'Share Big Live Market on WhatsApp',
      description: 'Share app link with 3 friends or groups',
      rewardPkr: 75,
      actionLabel: 'Share & Earn Rs. 75',
      iconType: 'share',
    ),
    const SponsoredMission(
      id: 4,
      title: 'Apple Store PK Follow & Review',
      description: 'Check out seller shop and leave constructive feedback',
      rewardPkr: 150,
      actionLabel: 'Visit Store (Rs. 150)',
      iconType: 'store',
    ),
  ];

  final Set<int> _completedMissions = {};

  void _completeMission(SponsoredMission mission) {
    setState(() {
      _completedMissions.add(mission.id);
    });

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const Icon(Icons.check_circle_rounded, color: Color(0xFF34D399), size: 20),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                '🎉 Mission complete! Rs. ${mission.rewardPkr} added to your wallet!',
                style: const TextStyle(fontWeight: FontWeight.w600),
              ),
            ),
          ],
        ),
        backgroundColor: const Color(0xFF0F172A),
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 3),
      ),
    );

    widget.onMissionCompleted?.call(mission);
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Color(0xFF0B0F19),
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 32),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Drag handle
          Center(
            child: Container(
              width: 44,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.white24,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ),
          const SizedBox(height: 16),

          // Header
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFFF59E0B), Color(0xFFD97706)],
                  ),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.stars_rounded, color: Colors.white, size: 24),
              ),
              const SizedBox(width: 12),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Daily Earning Missions',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                      ),
                    ),
                    Text(
                      'روزانہ ٹاسک اور انعامات حاصل کریں',
                      style: TextStyle(
                        fontSize: 12,
                        color: Color(0xFF94A3B8),
                      ),
                    ),
                  ],
                ),
              ),
              GestureDetector(
                onTap: () => Navigator.pop(context),
                child: const Icon(Icons.close_rounded, color: Colors.white54),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Missions List
          Flexible(
            child: ListView.separated(
              shrinkWrap: true,
              itemCount: _missions.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (context, index) {
                final mission = _missions[index];
                final isDone = _completedMissions.contains(mission.id);

                return Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: const Color(0xFF131B2E),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: isDone
                          ? const Color(0xFF34D399).withValues(alpha: 0.4)
                          : const Color(0xFF334155),
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: isDone
                              ? const Color(0xFF34D399).withValues(alpha: 0.15)
                              : const Color(0xFF38BDF8).withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Icon(
                          isDone
                              ? Icons.check_circle_rounded
                              : mission.iconType == 'video'
                                  ? Icons.play_circle_fill_rounded
                                  : mission.iconType == 'survey'
                                      ? Icons.quiz_rounded
                                      : mission.iconType == 'share'
                                          ? Icons.share_rounded
                                          : Icons.store_rounded,
                          color: isDone ? const Color(0xFF34D399) : const Color(0xFF38BDF8),
                          size: 22,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              mission.title,
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w700,
                                color: isDone ? const Color(0xFF94A3B8) : Colors.white,
                                decoration: isDone ? TextDecoration.lineThrough : null,
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              mission.description,
                              style: const TextStyle(
                                fontSize: 11,
                                color: Color(0xFF64748B),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 8),
                      GestureDetector(
                        onTap: isDone ? null : () => _completeMission(mission),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
                          decoration: BoxDecoration(
                            gradient: isDone
                                ? null
                                : const LinearGradient(
                                    colors: [Color(0xFF38BDF8), Color(0xFF2563EB)],
                                  ),
                            color: isDone ? const Color(0xFF1E293B) : null,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            isDone ? 'Completed' : 'Rs. ${mission.rewardPkr}',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w800,
                              color: isDone ? const Color(0xFF64748B) : Colors.white,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
