using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCData.Data;
using PHCDto.DTO;
using PHCRepos.Repos.Core;
using PHCRepos.Repos.AbstractContracts;
using System.Data.Objects;

namespace PHCRepos.Repos.Core
{
    public class PetProfileDbProc : IPetProfileDbProc
    {
        PHCEntities context;

        public PetProfileDbProc(PHCEntities _phc)
        {
            context = _phc;
        }

        public override PetProfileDTO SavePetProf(PetProfileDTO dto)
        {
            try
            {
                PetProfile pf = new PetProfile();
                pf.UID = dto.UID;
                pf.PetCategory = dto.pcid.ToString();
                pf.PetName = dto.PetName;
                pf.PetBreed = dto.PetBreed;
                context.AddToPetProfiles(pf);
                context.SaveChanges();
                PetProfileDTO pdto = new PetProfileDTO();
                pdto.PID = pf.PID;
                pdto.PetName = pf.PetName;
                pdto.pcid = Convert.ToInt32(pf.PetCategory);
                return pdto;
            }
            catch (Exception ex)
            {
                throw ex;
            }
        }

        public override PetProfileDTO GetPetDetails(int PID)
        {
            try
            {
                PetProfileDTO dto = new PetProfileDTO();
                PetProfile ptdet = context.PetProfiles.Where(p => p.PID == PID).SingleOrDefault();
                if (ptdet != null)
                {
                    dto.PID = ptdet.PID;
                    dto.UID = ptdet.UID;
                    dto.PetName = ptdet.PetName;
                    dto.PetBreed = ptdet.PetBreed;
                    dto.pcid = Convert.ToInt32(ptdet.PetCategory);
                }
                return dto;
            }
            catch (Exception ex)
            {

                throw ex;
            }
        }

        public override int Update_petProfile(ProfileDTO pdto)
        {
            try
            {
                if (pdto.PID != 0)
                {
                    PetProfile pdet = context.PetProfiles.Where(p => p.PID == pdto.PID).SingleOrDefault();
                    if (pdet != null)
                    {
                        if (!string.IsNullOrEmpty(pdto.PetName))
                            pdet.PetName = pdto.PetName;
                        if (!string.IsNullOrEmpty(pdto.PetBreed))
                            pdet.PetBreed = pdto.PetBreed;
                        if (pdto.pcid == 1 || pdto.pcid == 2)
                            pdet.PetCategory = Convert.ToString(pdto.pcid);
                        if (!string.IsNullOrEmpty(pdto.PetImage))
                            pdet.PetImage = pdto.PetImage;
                        if (!string.IsNullOrEmpty(pdto.Location))
                            pdet.Location = pdto.Location;
                        if (!string.IsNullOrEmpty(pdto.DOBDate))
                            pdet.DOB = Convert.ToDateTime(pdto.DOBDate);
                        if (!string.IsNullOrEmpty(pdto.About))
                            pdet.About = pdto.About;
                        if (!string.IsNullOrEmpty(pdto.FavProduct))
                            pdet.FavProduct = pdto.FavProduct;

                        context.PetProfiles.ApplyCurrentValues(pdet);
                        context.SaveChanges();

                        int uid=Convert.ToInt32(pdet.UID);
                        int pid = pdto.PID;
                        PetProfile pcnt = context.PetProfiles.Where(k => k.UID == uid).First();
                        int pp = pcnt.PID;

                        if (pid == pp)
                        {
                            PetProfile peb = context.PetProfiles.Where(p => p.PID == pid).SingleOrDefault();

                            if (peb.UID != null && peb.PetName != null && peb.PetCategory != null && peb.PetImage != null && peb.Location != null && peb.DOB != null && peb.About != null)
                            {
                                if (!UserHasBadge(peb.UID.Value, 22))
                                {
                                    if( peb.SecondBadge == null )
                                        peb.SecondBadge = 1;
                                    else
                                        peb.SecondBadge = 2;
                                    context.PetProfiles.ApplyCurrentValues(peb);
                                    context.SaveChanges();
                                    // Second Badge
                                    PetEarnBadge pbearn = new PetEarnBadge();
                                    pbearn.UID = peb.UID;
                                    pbearn.PID = pid;
                                    pbearn.BID = 22;
                                    pbearn.BadgeEarn = true;
                                    pbearn.ChCompleteDate = DateTime.Now;
                                    context.AddToPetEarnBadges(pbearn);
                                    context.SaveChanges();
                                }
                                pdto.PID = 1;
                            }
                            else
                            {
                                peb.SecondBadge = 2;
                                context.PetProfiles.ApplyCurrentValues(peb);
                                context.SaveChanges();
                            }
                        }
                        else
                        {
                            pdto.PID = 0;
                        }
                    }                 
                }
                return pdto.PID;
            }
            catch (Exception ex)
            {

                throw ex;
            }
        }

        public override ProfileDTO GetLastPetReg(int uid)
        {
            using (var context = new PHCEntities())
            {
                ProfileDTO pdto = null;
                try
                {

                    PetProfile pp = context.PetProfiles.Where(p => p.UID == uid).OrderBy(p => p.PID).FirstOrDefault();
                    if (pp != null)
                    {
                        pdto = new ProfileDTO();
                        pdto.PID = pp.PID;
                        pdto.UID = pp.UID;
                        pdto.PetName = pp.PetName;
                        pdto.PetBreed = pp.PetBreed;
                        pdto.petcat = pp.PetCategory;
                        pdto.secondbdg =Convert.ToInt32(pp.SecondBadge);
                        if (pp.PetImage != null)
                        {
                            pdto.PetImage = "../../content/uploads/" + pp.PetImage;
                            pdto.pimg = pp.PetImage;
                        }
                        else
                        {
                            pdto.PetImage = "../../content/images/" + "Noimage1.jpg";
                        }



                        pdto.Location = pp.Location;
                        pdto.DOB = Convert.ToDateTime(pp.DOB);
                        string dob = null;
                        dob = string.Format("{0:MM/dd/yyyy}", pdto.DOB);

                        if (dob == "01/01/0001")
                        {
                            pdto.DOBDate = "";
                        }
                        else
                        {
                            pdto.DOBDate = dob;
                        }

                        pdto.About = pp.About;
                        pdto.FavProduct = pp.FavProduct;
                        pdto.pcid = Convert.ToInt32(pp.PetCategory);

                        ProfileDTO adto = null;
                        List<ProfileDTO> ilst = new List<ProfileDTO>();
                        List<PetProfile> lstdo = context.PetProfiles.Where(p => p.UID == uid && p.PID != pp.PID).ToList();
                        if (lstdo != null)
                        {
                            foreach (PetProfile p in lstdo)
                            {
                                adto = new ProfileDTO();
                                adto.PID = p.PID;
                                adto.UID = p.UID;
                                adto.PetName = p.PetName;
                                adto.PetBreed = p.PetBreed;
                                adto.petcat = p.PetCategory;
                                adto.PetImage = "../../content/uploads/" + p.PetImage;
                                adto.Location = p.Location;
                                adto.DOB = Convert.ToDateTime(p.DOB);
                                string dob1 = null;
                                dob1 = string.Format("{0:MM/dd/yyyy}", adto.DOB);

                                if (dob1 == "01/01/0001")
                                {
                                    adto.DOBDate = "";
                                }
                                else
                                {
                                    adto.DOBDate = dob1;
                                }


                                adto.About = p.About;
                                adto.FavProduct = p.FavProduct;
                                adto.pcid = Convert.ToInt32(p.PetCategory);
                                ilst.Add(adto);

                            }

                            pdto.ldto = ilst;
                        }

                    }
                    return pdto;

                }


                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override int SaveFirstBadge(int uid, int pid)
        {
            // Let's ensure they don't already have this badge
            if( UserHasBadge( uid, 1 ) )
                return 0;

            try
            {
                AssignBadge(uid, 1, pid);
                return 1;
            }
            catch (Exception)
            {
                return 0;
            }
        }

        /// <summary>
        /// Assigns a badge directly to the user -- should be used only for non-challange badges
        /// </summary>
        /// <param name="userId"></param>
        /// <param name="badgeId"></param>
        public override bool AssignBadge(int userId, int badgeId, int? petId = null)
        {
            using (var context = new PHCEntities())
            {
                if (UserHasBadge(userId, badgeId))
                    return false;
                
                PetEarnBadge p = new PetEarnBadge();
                p.UID = userId;
                p.BID = badgeId;
                p.PID = petId;
                p.BadgeEarn = true;
                p.ChCompleteDate = DateTime.Now;
                context.AddToPetEarnBadges(p);
                context.SaveChanges();
                return true;
            }
        }

        /// <summary>
        /// Checks to see if the user already has thsi badge
        /// </summary>
        /// <param name="uid">User Id</param>
        /// <param name="bid">Badge Id</param>
        /// <returns></returns>
        public override bool UserHasBadge(int uid, int bid)
        {
            using (var context = new PHCEntities())
            {
                var existing = context.PetEarnBadges.Where(b => b.BID == bid && b.UID == uid).FirstOrDefault();
                if (existing == null)
                    return false;
                return true;
            }
        }

        public override List<ChallengeDTO> ListChallenge()
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO chdto = null;
                    List<ChallengeDTO> lstdto = new List<ChallengeDTO>();

                    DateTime currentdate = DateTime.Now;
                   
                    // Pull  all the challanges data
                    List<Challenge> lstch = context.Challenges
                        .Where(k => k.ChallengeCreatedDate <= currentdate)
                        .OrderByDescending(a => a.ChallengeCreatedDate).ToList();

                    for (int i = 0; i < lstch.Count; i++)
                    {
                        chdto = new ChallengeDTO();
                        chdto.CHID = lstch[i].CHID;
                        chdto.ChallengeName = lstch[i].ChallengeName;
                        chdto.Description = lstch[i].Description;
                        chdto.ChcreatedDate = Convert.ToDateTime(lstch[i].ChallengeCreatedDate);
                        chdto.catchImg = lstch[i].CatChallengeImage;
                        chdto.dogchImg = lstch[i].DogChallengeImage;
                        chdto.Tagline = lstch[i].Tagline;
                        lstdto.Add(chdto);
                    }
                    
                    return lstdto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO GetChallenginfo(int id)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO dto = new ChallengeDTO();
                    Challenge c = context.Challenges.Where(u => u.CHID == id).SingleOrDefault();
                    if (c != null)
                    {
                        dto.CHID = c.CHID;
                        dto.ChallengeName = c.ChallengeName;
                        dto.Description = c.Description;
                        dto.CatDescription = c.CatDescription;
                        dto.catchImg = c.CatChallengeImage;
                        dto.dogchImg = c.DogChallengeImage;
                    }
                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override List<ChallengeDTO> GetPetnames(int uid, int cid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    List<ChallengeDTO> lstchdto = new List<ChallengeDTO>();
                    ChallengeDTO dto = null;
                    List<PetProfile> lstpetpro = context.PetProfiles.Where(u => u.UID == uid).ToList();
                    for (int i = 0; i < lstpetpro.Count; i++)
                    {
                        dto = new ChallengeDTO();
                        dto.PID = lstpetpro[i].PID;
                        dto.Petname = lstpetpro[i].PetName;
                        dto.CHID = cid;
                        PetEarnBadge peb = context.PetEarnBadges.Where(k => k.UID == uid && k.CID == cid && k.PID == dto.PID).SingleOrDefault();
                        if (peb != null)
                        {
                            dto.petearn = 1;
                        }
                        else
                        {
                            dto.petearn = 0;
                        }
                        lstchdto.Add(dto);
                    }
                    return lstchdto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override void SaveChallengeBadge(ChallengeDTO dt)
        {
            using (var context = new PHCEntities())
            {
                // Let's ensure they don't already have this badge
                if (UserHasBadge(dt.petid, dt.BID))
                    return;

                try
                {
                    PetEarnBadge peb = new PetEarnBadge();
                    peb.CID = dt.CHID;
                    if (dt.CHID != 0)
                    {
                        Challenge ch = context.Challenges.Where(u => u.CHID == dt.CHID).SingleOrDefault();
                        if (ch != null)
                        {
                            peb.BID = ch.BadgeID;
                            string cname = ch.ChallengeName;
                            if ((cname == "Share the Wellness") || (cname == "Super Sharer"))
                            {
                                peb.BadgeEarn = false;
                            }
                            else
                            {
                                peb.BadgeEarn = true;

                            }
                        }
                    }
                    peb.Description = dt.Description;
                    peb.Image = dt.userpetimg;
                    peb.PID = dt.PID;
                    peb.UID = dt.petid;
                    if (dt.PID != 0)
                    {
                        PetProfile pro = context.PetProfiles.Where(j => j.PID == dt.PID).SingleOrDefault();
                        if (pro != null)
                        {
                            peb.CatID = Convert.ToInt32(pro.PetCategory);
                        }
                    }

                    peb.ChCompleteDate = DateTime.Now;
                    context.AddToPetEarnBadges(peb);
                    context.SaveChanges();
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO Chkpetbadge(int pid, int cid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    PetEarnBadge peb = new PetEarnBadge();
                    peb = context.PetEarnBadges.Where(u => u.PID == pid && u.CID == cid).SingleOrDefault();
                    ChallengeDTO dto = new ChallengeDTO();
                    if (peb != null)
                    {
                        dto.PID = Convert.ToInt32(peb.PID);
                        dto.CHID = Convert.ToInt32(peb.CID);
                        dto.userpetimg = peb.Image;
                        dto.userdesc = peb.Description;
                    }
                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO GetBadges(int uid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    BadgesDTO bdto = null;
                    ChallengeDTO cdto = new ChallengeDTO();
                    List<BadgesDTO> lstbdto = new List<BadgesDTO>();
                    List<PetEarnBadge> lstpeb = new List<PetEarnBadge>();
                    lstpeb = context.PetEarnBadges.Where(u => u.UID == uid).ToList();

                    List<Badge> totbadges = new List<Badge>();


                    for (int j = 0; j < lstpeb.Count; j++)
                    {
                        int b = Convert.ToInt32(lstpeb[j].BID);
                        if (j == 0)
                        {
                            List<Badge> list = context.Badges.Where(l => l.BId != b).ToList();
                            totbadges = list;
                        }
                        else
                        {
                            totbadges = totbadges.Where(g => g.BId != b).ToList();
                        }
                    }


                    for (int i = 0; i < lstpeb.Count; i++)
                    {
                        bdto = new BadgesDTO();
                        bdto.EBID = lstpeb[i].EBID;
                        bdto.BID = Convert.ToInt32(lstpeb[i].BID);

                        if (lstpeb[i].BadgeEarn == true)
                        {
                            bdto.bstatus = 1;
                        }
                        else
                        {
                            bdto.bstatus = 0;
                        }

                        int bid = Convert.ToInt32(lstpeb[i].BID);
                        Badge b = new Badge();
                        b = context.Badges.Where(p => p.BId == bid).SingleOrDefault();
                        if (b != null)
                        {
                            bdto.BadgeImage = "../../Content/Uploads/Challenges/" + b.Badgelogo;
                        }
                        lstbdto.Add(bdto);
                    }

                    for (int c = 0; c < totbadges.Count; c++)
                    {
                        bdto = new BadgesDTO();
                        bdto.EBID = 0;
                        bdto.BID = Convert.ToInt32(totbadges[c].BId);
                        bdto.bstatus = 0;
                        bdto.BadgeImage = "../../Content/Uploads/Challenges/" + totbadges[c].Badgelogo;
                        lstbdto.Add(bdto);
                    }



                    cdto.lstbadge = lstbdto;
                    return cdto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ProfileDTO GetBadgeCount(int uid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    int cnt = 0;
                    cnt = context.PetEarnBadges.Where(u => u.UID == uid && u.BadgeEarn == true).Count();
                    ProfileDTO dto = new ProfileDTO();
                    dto.badgecount = cnt;
                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO CompleteCh(int ebid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO dto = new ChallengeDTO();

                    PetEarnBadge peb = new PetEarnBadge();
                    peb = context.PetEarnBadges.Where(u => u.EBID == ebid).SingleOrDefault();
                    if (peb != null)
                    {
                        dto.PID = Convert.ToInt32(peb.PID);
                        if (dto.PID != null)
                        {
                            PetProfile pp = new PetProfile();
                            pp = context.PetProfiles.Where(k => k.PID == dto.PID).SingleOrDefault();
                            if (pp != null)
                            {
                                dto.Petname = pp.PetName;
                                dto.pcid = Convert.ToInt32(pp.PetCategory);
                            }
                        }


                        dto.CHID = Convert.ToInt32(peb.CID);
                        if (dto.CHID != null)
                        {
                            Challenge ch = new Challenge();
                            ch = context.Challenges.Where(l => l.CHID == dto.CHID).SingleOrDefault();
                            if (ch != null)
                            {
                                dto.ChallengeName = ch.ChallengeName;
                                dto.Description = ch.Description;
                                dto.CatDescription = ch.CatDescription;

                                if (dto.pcid == 1)
                                {
                                    dto.dogchImg = ch.DogChallengeImage;
                                }
                                else
                                {
                                    dto.dogchImg = ch.CatChallengeImage;
                                }

                            }
                        }


                        dto.BID = Convert.ToInt32(peb.BID);
                        if (dto.BID != null)
                        {
                            Badge bd = new Badge();
                            bd = context.Badges.Where(h => h.BId == dto.BID).SingleOrDefault();
                            if (bd != null)
                            {
                                dto.bname = bd.Badgename;
                                dto.blogo = bd.Badgelogo;

                                if (dto.ChallengeName == "Super Sharer")
                                {
                                    if (peb.FBStatus == true && peb.TwitterStatus == true)
                                    {
                                        dto.cdescription = bd.CDescription;
                                    }
                                    else
                                    {
                                        dto.cdescription = bd.LDescription;
                                    }
                                }
                                else if (dto.ChallengeName == "Share the Wellness")
                                {
                                    if (peb.FBStatus == true || peb.TwitterStatus == true)
                                    {
                                        dto.cdescription = bd.CDescription;
                                    }
                                    else
                                    {
                                        dto.cdescription = bd.LDescription;
                                    }
                                }
                                else
                                {
                                    dto.ldescription = bd.LDescription;
                                    dto.cdescription = bd.CDescription;
                                }

                            }
                        }

                        if (peb.FBStatus != null)
                        {
                            dto.FBStatus = false;
                        }
                        else
                        {
                            dto.FBStatus = Convert.ToBoolean(peb.FBStatus);
                        }

                        if (peb.TwitterStatus != null)
                        {
                            dto.TwitStatus = false;
                        }
                        else
                        {
                            dto.TwitStatus = Convert.ToBoolean(peb.FBStatus);
                        }

                        dto.userdesc = peb.Description;
                        dto.userpetimg = peb.Image;
                        dto.ChcreatedDate = Convert.ToDateTime(peb.ChCompleteDate);

                        string dob = null;
                        dob = string.Format("{0:MM/dd/yyyy}", peb.ChCompleteDate);
                        dto.SChcreatedDate = dob;
                    }

                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override int chkuser(int uid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    int petcnt = 0;
                    User u = new User();
                    u = context.Users.Where(d => d.UID == uid).SingleOrDefault();
                    if (u != null)
                    {
                        petcnt = context.PetProfiles.Where(k => k.UID == uid).Count();
                    }
                    return petcnt;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override PetProfileDTO SaveFirstPet(ProfileDTO dto)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    PetProfileDTO pdto = new PetProfileDTO();
                    PetProfile pp = new PetProfile();
                    pp.PetName = dto.PetName;
                    pp.PetBreed = dto.PetBreed;
                    pp.PetCategory = dto.pcid.ToString();
                    pp.UID = dto.UID;
                    context.AddToPetProfiles(pp);
                    context.SaveChanges();
                    pdto.PID = pp.PID;
                    return pdto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

       

        public override ProfileDTO GetRewards(int uid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ProfileDTO pdto = new ProfileDTO();
                    int badgerewardcnt = 0;
                    badgerewardcnt = context.PetEarnBadges.Where(u => u.UID == uid && u.BadgeEarn==true).Count();
                    int rwdcnt = context.Rewards.Where(u => u.UID == uid).Count();
                    pdto.badgecount = (badgerewardcnt - rwdcnt);
                    return pdto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO chkchallinfo(int petid, int chid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO dto = new ChallengeDTO();
                    PetEarnBadge peb = context.PetEarnBadges.Where(u => u.PID == petid && u.CID == chid).SingleOrDefault();
                    if (peb != null)
                    {
                        dto.userdesc = peb.Description;
                        dto.userpetimg = peb.Image;
                    }
                    else
                    {
                        dto = null;
                    }
                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO PreviousChallenges()
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO dto = new ChallengeDTO();
                    ChallengeDTO chdto = null;
                    List<ChallengeDTO> lstdto = new List<ChallengeDTO>();
                    List<Challenge> lst = new List<Challenge>();

                    DateTime currentdate = DateTime.Now;

                    lst = context.Challenges.Where(k => EntityFunctions.DiffDays(k.ChallengeCreatedDate, currentdate) > 0).ToList();

                    lst = lst.OrderByDescending(i => i.ChallengeCreatedDate).ToList();

                    for (int i = 0; i < lst.Count; i++)
                    {
                        if (i > 5)
                        {
                            chdto = new ChallengeDTO();
                            chdto.CHID = lst[i].CHID;
                            chdto.ChallengeName = lst[i].ChallengeName;
                            chdto.Description = lst[i].Description;
                            chdto.ChcreatedDate = Convert.ToDateTime(lst[i].ChallengeCreatedDate);
                            chdto.dogchImg = lst[i].DogChallengeImage;
                            chdto.catchImg = lst[i].CatChallengeImage;
                            lstdto.Add(chdto);
                        }
                    }
                    dto.lstchdto = lstdto.OrderByDescending(p => p.ChcreatedDate).ToList(); ;
                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override string Delete_pet(int id)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    PetProfile pr = context.PetProfiles.Where(p => p.PID == id).SingleOrDefault();

                    context.DeleteObject(pr);
                    context.SaveChanges();
                    msg = "Deleted";
                }
                catch (Exception ex)
                {
                    msg = ex.Message;

                }


                return msg;
            }
        }

        public override int savepetprofile(ProfileDTO pdto)
        {
            try
            {
                if (pdto.PetName1 != null && pdto.pimg1 != null)
                {
                    PetProfile pf = new PetProfile();
                    pf.UID = pdto.UID;
                    pf.PetCategory = pdto.pcid1.ToString();
                    pf.PetName = pdto.PetName1;
                    //pf.PetBreed = pdto.PetBreed;
                    if (pdto.DOBDate1 != null)
                    {
                        pf.DOB = Convert.ToDateTime(pdto.DOBDate1);
                    }
                    pf.Location = pdto.Location1;
                    pf.FavProduct = pdto.FavProduct1;
                    pf.PetImage = pdto.pimg1;
                    pf.About = pdto.About1;
                    context.AddToPetProfiles(pf);
                    context.SaveChanges();
                    pdto.PID = pf.PID;
                }

                return pdto.PID;
            }
            catch (Exception ex)
            {
                throw ex;
            }

        }

        public override ProfileDTO geteditpetprofile(int id)
        {
            using (var context = new PHCEntities())
            {
                ProfileDTO pdto = null;
                try
                {
                    PetProfile pp = context.PetProfiles.Where(p => p.PID == id).SingleOrDefault();
                    if (pp != null)
                    {
                        pdto = new ProfileDTO();
                        pdto.PID = pp.PID;
                        pdto.UID = pp.UID;
                        pdto.PetName = pp.PetName;
                        pdto.PetBreed = pp.PetBreed;
                        pdto.petcat = pp.PetCategory;

                        if (pp.PetImage != null)
                        {
                            pdto.PetImage = pp.PetImage;
                            pdto.pimg = pp.PetImage;
                        }
                        else
                        {
                            pdto.PetImage = "Noimage1.jpg";
                        }

                        pdto.Location = pp.Location;
                        pdto.DOB = Convert.ToDateTime(pp.DOB);
                        string dob = null;
                        dob = string.Format("{0:MM/dd/yyyy}", pdto.DOB);

                        if (dob == "01/01/0001")
                        {
                            pdto.DOBDate = "";
                        }
                        else
                        {
                            pdto.DOBDate = dob;
                        }

                        pdto.About = pp.About;
                        pdto.FavProduct = pp.FavProduct;
                        pdto.pcid = Convert.ToInt32(pp.PetCategory);

                    }
                    return pdto;

                }


                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO petCompleteCh(int ebid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    Challenge ch = new Challenge();
                    ChallengeDTO dto = null;
                    ch = context.Challenges.Where(u => u.CHID == ebid).SingleOrDefault();
                    if (ch != null)
                    {
                        dto = new ChallengeDTO();
                        dto.CHID = ch.CHID;
                        dto.ChallengeName = ch.ChallengeName;
                        dto.dogchImg = ch.DogChallengeImage;
                    }
                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO InCompleteCh(int bid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO dto = new ChallengeDTO();
                    if (bid != 0)
                    {
                        Badge bd = new Badge();
                        bd = context.Badges.Where(h => h.BId == bid).SingleOrDefault();
                        if (bd != null)
                        {
                            dto.bname = bd.Badgename;
                            dto.blogo = bd.Badgelogo;
                            dto.ChallengeName = bd.LDescription;                          
                        }
                    }
                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO CHNotify(int uid)
        {
            try
            {
                ChallengeDTO dto = null;
                PetEarnBadge peb = context.PetEarnBadges.Where(u => u.UID == uid).OrderByDescending(p => p.EBID).FirstOrDefault();
                if (peb != null)
                {
                    dto = new ChallengeDTO();
                    dto.CHID = Convert.ToInt32(peb.CID);
                    if (dto.CHID != null)
                    {
                        Challenge ch = context.Challenges.Where(k => k.CHID == dto.CHID).SingleOrDefault();
                        if (ch != null)
                        {
                            dto.ChallengeName = ch.ChallengeName;
                        }
                    }

                    dto.BID = Convert.ToInt32(peb.BID);
                    if (dto.BID != null)
                    {
                        Badge bd = context.Badges.Where(l => l.BId == dto.BID).SingleOrDefault();
                        if (bd != null)
                        {
                            dto.blogo = bd.Badgelogo;
                            dto.bname = bd.Badgename;
                        }
                    }

                    dto.PID = Convert.ToInt32(peb.PID);
                    if (dto.PID != null)
                    {
                        PetProfile pp = context.PetProfiles.Where(j => j.PID == dto.PID).SingleOrDefault();
                        if (pp != null)
                        {
                            dto.Petname = pp.PetName;
                        }
                    }

                    dto.userpetimg = peb.Image;
                }
                return dto;
            }
            catch (Exception ex)
            {
                throw ex;
            }
        }

        public override int ChUserCnt(int cid, int uid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    int cnt = context.PetEarnBadges.Where(u => u.UID == uid && u.CID == cid).Count();
                    return cnt;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override ChallengeDTO CHDExists(int chid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO dto = null;

                    Challenge ch = context.Challenges.Where(u => u.CHID == chid).SingleOrDefault();
                    if (ch != null)
                    {
                        dto = new ChallengeDTO();
                        dto.ChallengeName = ch.ChallengeName;
                        dto.BID = Convert.ToInt32(ch.BadgeID);
                        if (dto.BID != 0)
                        {
                            Badge bd = context.Badges.Where(k => k.BId == dto.BID).SingleOrDefault();
                            if (bd != null)
                            {
                                dto.blogo = bd.Badgelogo;
                                dto.bname = bd.Badgename;
                            }
                        }
                    }

                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }


        public override void PinUpdate(int ebid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    PetEarnBadge p = new PetEarnBadge();
                    p = context.PetEarnBadges.Where(u => u.EBID == ebid).SingleOrDefault();
                    if (p != null)
                    {
                        Challenge ch = context.Challenges.Where(k => k.CHID == p.CID).SingleOrDefault();
                        if (ch != null)
                        {
                            string cname = ch.ChallengeName;
                            if ((cname == "Share the Wellness") || (cname == "Super Sharer"))
                            {
                                p.Pinterest = true;
                                if (cname == "Super Sharer")
                                {
                                    if (p.TwitterStatus == true && p.FBStatus == true)
                                    {
                                        p.BadgeEarn = true;
                                    }
                                }
                                else
                                {
                                    p.BadgeEarn = true;
                                }
                            }
                            else
                            {
                                p.Pinterest = true;
                            }
                            context.PetEarnBadges.ApplyCurrentValues(p);
                            context.SaveChanges();
                        }
                    }

                    if (p.BID == 1)
                    {
                        p.Pinterest = true;
                        context.PetEarnBadges.ApplyCurrentValues(p);
                        context.SaveChanges();
                    }
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override void TweetUpdate(int ebid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    PetEarnBadge p = new PetEarnBadge();
                    p = context.PetEarnBadges.Where(u => u.EBID == ebid).SingleOrDefault();
                    if (p != null)
                    {
                        Challenge ch = context.Challenges.Where(k => k.CHID == p.CID).SingleOrDefault();
                        if (ch != null)
                        {
                            string cname = ch.ChallengeName;
                            if ((cname == "Share the Wellness") || (cname == "Super Sharer"))
                            {
                                p.TwitterStatus = true;
                                if (cname == "Super Sharer")
                                {
                                    if (p.FBStatus == true && p.Pinterest == true)
                                    {
                                        p.BadgeEarn = true;
                                    }
                                }
                                else
                                {
                                    p.BadgeEarn = true;
                                }
                            }
                            else
                            {
                                p.TwitterStatus = true;
                            }
                            context.PetEarnBadges.ApplyCurrentValues(p);
                            context.SaveChanges();
                        }
                    }

                    if (p.BID == 1)
                    {
                        p.TwitterStatus = true;
                        context.PetEarnBadges.ApplyCurrentValues(p);
                        context.SaveChanges();
                    }
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override void UpdateFBStatus(int ebid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    PetEarnBadge p = new PetEarnBadge();
                    p = context.PetEarnBadges.Where(u => u.EBID == ebid).SingleOrDefault();
                    if (p != null)
                    {
                        Challenge ch = context.Challenges.Where(k => k.CHID == p.CID).SingleOrDefault();
                        if (ch != null)
                        {
                            string cname = ch.ChallengeName;
                            if ((cname == "Share the Wellness") || (cname == "Super Sharer"))
                            {
                                p.FBStatus = true;
                                if (cname == "Super Sharer")
                                {
                                    if (p.TwitterStatus == true && p.Pinterest == true)
                                    {
                                        p.BadgeEarn = true;
                                    }
                                }
                                else
                                {
                                    p.BadgeEarn = true;
                                }
                            }
                            else
                            {
                                p.FBStatus = true;
                            }
                            context.PetEarnBadges.ApplyCurrentValues(p);
                            context.SaveChanges();
                        }
                    }

                    if (p.BID == 1)
                    {
                        p.FBStatus = true;
                        context.PetEarnBadges.ApplyCurrentValues(p);
                        context.SaveChanges();
                    }

                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override RewardsDTO GetWallPaperList()
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    List<RewardsDTO> lstrdto = new List<RewardsDTO>();
                    RewardsDTO ldto = new RewardsDTO();
                    RewardsDTO dto = null;

                    List<Wallpaper> lstwall = context.Wallpapers.ToList();

                    for (int i = 0; i < lstwall.Count; i++)
                    {
                        dto = new RewardsDTO();
                        dto.WID = lstwall[i].WID;
                        dto.WallImage = "../../Content/Uploads/Challenges/" + lstwall[i].WallImage;
                        dto.Title = lstwall[i].Title;
                        dto.image = lstwall[i].WallImage;
                        lstrdto.Add(dto);
                    }
                    ldto.lstrewards = lstrdto;

                    return ldto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override string UpdateRewards(int uid,int wid)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    string ImgURL = "";
                    Reward rwd = new Reward();
                    rwd.UID = uid;
                    rwd.WID = wid;
                    context.AddToRewards(rwd);
                    context.SaveChanges();

                    Wallpaper w = context.Wallpapers.Where(u => u.WID == wid).SingleOrDefault();
                    if (w != null)
                    {
                        ImgURL = w.WallImage;
                        return ImgURL;
                    }
                    else
                    {
                        return null;
                    }                   
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }


        public override ChallengeDTO GetEarnedBadgeByChallenge(int challengeId, int userId)
        {
            using (var context = new PHCEntities())
            {

                try
                {

                    var record = context.PetEarnBadges.Where(u => u.UID == userId && u.CID == challengeId).SingleOrDefault();

                    ChallengeDTO dto = new ChallengeDTO();

                    PetEarnBadge peb = new PetEarnBadge();
                    peb = context.PetEarnBadges.Where(u => u.EBID == record.EBID).SingleOrDefault();

                    if (peb != null)
                    {
                        dto.PID = Convert.ToInt32(peb.PID);
                        if (dto.PID != null)
                        {
                            PetProfile pp = new PetProfile();
                            pp = context.PetProfiles.Where(k => k.PID == dto.PID).SingleOrDefault();
                            if (pp != null)
                            {
                                dto.Petname = pp.PetName;
                                dto.pcid = Convert.ToInt32(pp.PetCategory);
                            }
                        }


                        dto.CHID = Convert.ToInt32(peb.CID);
                        if (dto.CHID != null)
                        {
                            Challenge ch = new Challenge();
                            ch = context.Challenges.Where(l => l.CHID == dto.CHID).SingleOrDefault();
                            if (ch != null)
                            {
                                dto.ChallengeName = ch.ChallengeName;
                                dto.Description = ch.Description;
                                dto.CatDescription = ch.CatDescription;

                                if (dto.pcid == 1)
                                {
                                    dto.dogchImg = ch.DogChallengeImage;
                                }
                                else
                                {
                                    dto.dogchImg = ch.CatChallengeImage;
                                }

                            }
                        }


                        dto.BID = Convert.ToInt32(peb.BID);
                        if (dto.BID != null)
                        {
                            Badge bd = new Badge();
                            bd = context.Badges.Where(h => h.BId == dto.BID).SingleOrDefault();
                            if (bd != null)
                            {
                                dto.bname = bd.Badgename;
                                dto.blogo = bd.Badgelogo;

                                if (dto.ChallengeName == "Super Sharer")
                                {
                                    if (peb.FBStatus == true && peb.TwitterStatus == true)
                                    {
                                        dto.cdescription = bd.CDescription;
                                    }
                                    else
                                    {
                                        dto.cdescription = bd.LDescription;
                                    }
                                }
                                else if (dto.ChallengeName == "Share the Wellness")
                                {
                                    if (peb.FBStatus == true || peb.TwitterStatus == true)
                                    {
                                        dto.cdescription = bd.CDescription;
                                    }
                                    else
                                    {
                                        dto.cdescription = bd.LDescription;
                                    }
                                }
                                else
                                {
                                    dto.ldescription = bd.LDescription;
                                    dto.cdescription = bd.CDescription;
                                }

                            }
                        }

                        if (peb.FBStatus != null)
                        {
                            dto.FBStatus = false;
                        }
                        else
                        {
                            dto.FBStatus = Convert.ToBoolean(peb.FBStatus);
                        }

                        if (peb.TwitterStatus != null)
                        {
                            dto.TwitStatus = false;
                        }
                        else
                        {
                            dto.TwitStatus = Convert.ToBoolean(peb.FBStatus);
                        }

                        dto.userdesc = peb.Description;
                        dto.userpetimg = peb.Image;
                        dto.ChcreatedDate = Convert.ToDateTime(peb.ChCompleteDate);

                        string dob = null;
                        dob = string.Format("{0:MM/dd/yyyy}", peb.ChCompleteDate);
                        dto.SChcreatedDate = dob;
                    }

                    return dto;
                }
                catch (Exception ex)
                {

                    throw ex;
                }

                

            }
        }

    }


}
